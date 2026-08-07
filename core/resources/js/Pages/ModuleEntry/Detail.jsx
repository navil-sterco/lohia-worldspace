import React, { useState, useEffect, useRef } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { useModal } from '@/hooks/useModal';
import {
  GripVertical,
  Plus,
  Trash2,
  ChevronDown,
  ChevronUp,
  X,
  Blocks,
  Save,
  Upload,
  Image as ImageIcon,
  Video,
  File,
  Boxes,
  Search,
  Type,
  Calendar,
  Hash,
  Mail,
  Palette,
  CheckSquare,
  Radio,
  FileText,
  Code,
  LayoutTemplate,
  Layers,
  Repeat,
  Braces,
  Table,
} from 'lucide-react';
import WisyswigEditor from '@/Components/Fields/WisyswigEditor';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal';
import useCtrlSSubmit from '@/hooks/useCtrlSSubmit';

/** Keys kept only in React state — not stored inside section_data JSON */
const SECTION_BUILDER_UI_KEYS = new Set([
  'section_id', 'pivot_id', 'order', 'data', 'mapping_items',
  'isCollapsed', 'files', 'mapping_files', 'isNew', 'dirty', 'originalFingerprint',
]);

const pickPersistedExtrasFromParsed = (parsed) => {
  if (!parsed || typeof parsed !== 'object') return {};
  const out = {};
  Object.keys(parsed).forEach((k) => {
    if (k !== 'data' && k !== 'mapping_items') out[k] = parsed[k];
  });
  return out;
};

const pickPersistedExtrasFromSection = (section) => {
  if (!section || typeof section !== 'object') return {};
  const out = {};
  Object.keys(section).forEach((k) => {
    if (!SECTION_BUILDER_UI_KEYS.has(k)) out[k] = section[k];
  });
  return out;
};

const buildSectionDataJsonPayload = (section) => ({
  data: section.data ?? {},
  mapping_items: section.mapping_items && typeof section.mapping_items === 'object' && !Array.isArray(section.mapping_items)
    ? section.mapping_items
    : {},
  ...pickPersistedExtrasFromSection(section),
});

const splitNonEmptyLines = (text) =>
  (text || '').replace(/\r\n/g, '\n').split('\n').map((l) => l.replace(/\s+$/, '')).filter((l) => l.length > 0);

const parseDelimitedLine = (line) => {
  if (line.includes('\t')) return line.split('\t');
  return line.split(',').map((c) => c.trim().replace(/^"|"$/g, ''));
};

const cellValueFromPaste = (field, raw) => {
  if (field?.type === 'checkbox') {
    const t = String(raw ?? '').trim().toLowerCase();
    return t === '1' || t === 'true' || t === 'yes';
  }
  if (field?.type === 'number') {
    const n = parseFloat(String(raw ?? '').trim());
    return Number.isFinite(n) ? n : '';
  }
  return raw == null ? '' : String(raw);
};

const Detail = () => {
  const { props } = usePage();
  const { activeSections = [], sections: availableSections = [], entry, module, entryLabel, images, flash } = props;

  const backUrl = route('modules.entries.index', module.id);

  const getSectionStoreRoute = () => {
    return route('modules.entries.detail.store', { module: module.id, entry: entry.id });
  };

  const getSectionDestroyRoute = (pivotId) => {
    return '#';
  };

  const [sections, setSections] = useState([]);
  const [draggedSection, setDraggedSection] = useState(null);
  const [showComponentPicker, setShowComponentPicker] = useState(false);
  const [isSaving, setIsSaving] = useState(false);
  const [filePreviews, setFilePreviews] = useState({});
  const [isDraggable, setIsDraggable] = useState(false);
  const [deleteTarget, setDeleteTarget] = useState(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [itemsPerPage] = useState(30);

  const { modalRef, show: showModal, hide: hideModal } = useModal();
  const searchInputRef = useRef(null);
  const [processingDelete, setProcessingDelete] = useState(false);

  const fieldTypeIcons = {
    text: <Type size={13} />,
    textarea: <FileText size={13} />,
    code: <Code size={13} />,
    number: <Hash size={13} />,
    email: <Mail size={13} />,
    url: <Mail size={13} />,
    select: <ChevronDown size={13} />,
    checkbox: <CheckSquare size={13} />,
    radio: <Radio size={13} />,
    file: <Upload size={13} />,
    image: <ImageIcon size={13} />,
    date: <Calendar size={13} />,
    color: <Palette size={13} />,
  };

  const isFileObject = (obj) => obj && typeof obj === 'object' && typeof obj.name === 'string' && typeof obj.type === 'string' && typeof obj.size === 'number';

  useEffect(() => {
    if (flash?.success) toast.success(flash.success);
    if (flash?.error) toast.error(flash.error);
  }, [flash]);

  useEffect(() => {
    if (activeSections && activeSections.length > 0) {
      const formatted = activeSections.map((activeSection, index) => {
        let parsedData = {};
        let mappingItemsByGroup = {};
        let parsed = null;
        if (activeSection.pivot?.section_data) {
          try {
            parsed = JSON.parse(activeSection.pivot.section_data);
            if (parsed) {
              parsedData = parsed.data || {};
              mappingItemsByGroup = normalizeMappingItemsByGroup(parsed.mapping_items);
            }
          } catch (e) {
            parsedData = {};
            parsed = null;
          }
        }
        const persistExtras = pickPersistedExtrasFromParsed(parsed);
        return {
          section_id: activeSection.id,
          pivot_id: activeSection.pivot?.id,
          order: activeSection.pivot?.order ?? index,
          data: parsedData,
          mapping_items: mappingItemsByGroup,
          ...persistExtras,
          isCollapsed: true,
          files: {},
          mapping_files: {},
          isNew: false,
          dirty: false,
          originalFingerprint: createSectionFingerprint({
            section_id: activeSection.id,
            order: activeSection.pivot?.order ?? index,
            data: parsedData,
            mapping_items: mappingItemsByGroup,
            ...persistExtras,
          })
        };
      });
      setSections(formatted);
      setTimeout(() => {
        const allPreviews = {};
        formatted.forEach((section, index) => {
          Object.entries(section.data).forEach(([key, value]) => {
            if (typeof value === 'string' && value &&
              (value.match(/\.(jpg|jpeg|png|gif|svg|webp)$/i) ||
                value.includes('assets/img/') ||
                value.includes('assets/images/') ||
                value.startsWith('http'))) {
              allPreviews[`${index}_${key}`] = value;
            }
          });
          Object.entries(section.mapping_items || {}).forEach(([groupName, items]) => {
            (items || []).forEach((it, itemIndex) => {
              Object.entries(it || {}).forEach(([fieldName, value]) => {
                if (typeof value === 'string' && value &&
                  (value.match(/\.(jpg|jpeg|png|gif|svg|webp)$/i) ||
                    value.includes('assets/img/') ||
                    value.includes('assets/images/') ||
                    value.startsWith('http'))) {
                  allPreviews[`${index}_mapping_${groupName}_${itemIndex}_${fieldName}`] = value;
                }
                if (Array.isArray(value) && value.length > 0 && typeof value[0] === 'object' && value[0] !== null && !isFileObject(value[0])) {
                  value.forEach((nested, ni) => {
                    Object.entries(nested || {}).forEach(([nf, nv]) => {
                      if (typeof nv === 'string' && nv &&
                        (nv.match(/\.(jpg|jpeg|png|gif|svg|webp)$/i) ||
                          nv.includes('assets/img/') ||
                          nv.includes('assets/images/') ||
                          nv.startsWith('http'))) {
                        allPreviews[`${index}_mapping_${groupName}_${itemIndex}_${fieldName}_${ni}_${nf}`] = nv;
                      }
                    });
                  });
                }
              });
            });
          });
        });
        setFilePreviews(allPreviews);
      }, 100);
    } else {
      setSections([]);
    }
  }, [activeSections]);

  useEffect(() => {
    if (showComponentPicker && searchInputRef.current) {
      setTimeout(() => searchInputRef.current.focus(), 100);
    }
  }, [showComponentPicker]);

  const filteredSections = availableSections.filter(section =>
    searchTerm === '' ||
    section.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    section.identifier.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const totalPages = Math.ceil(filteredSections.length / itemsPerPage);
  const startIndex = (currentPage - 1) * itemsPerPage;
  const paginatedSections = filteredSections.slice(startIndex, startIndex + itemsPerPage);

  const isAssocObject = (obj) => obj && typeof obj === 'object' && !Array.isArray(obj);

  const normalizeMappingConfig = (raw) => {
    if (!Array.isArray(raw)) return [];
    if (raw[0] && isAssocObject(raw[0]) && Array.isArray(raw[0].fields)) {
      return raw.map((g) => ({
        group_label: g.group_label || 'Repeatable Items',
        group_name: g.group_name || 'items',
        parent_group: typeof g.parent_group === 'string' && g.parent_group.trim() ? g.parent_group.trim() : '',
        fields: Array.isArray(g.fields) ? g.fields : [],
      }));
    }
    if (raw[0] && isAssocObject(raw[0]) && typeof raw[0].name === 'string') {
      return [{ group_label: 'Repeatable Items', group_name: 'items', parent_group: '', fields: raw }];
    }
    return [];
  };

  const getChildMappingGroups = (groups, parentGroupName) =>
    (groups || []).filter((g) => g.parent_group && g.parent_group === parentGroupName);

  const isTopLevelMappingGroup = (g) => !g.parent_group || String(g.parent_group).trim() === '';

  const normalizeMappingItemsByGroup = (mi) => {
    if (Array.isArray(mi)) return { items: mi };
    if (isAssocObject(mi)) return mi;
    return {};
  };

  const createSectionFingerprint = (section) => JSON.stringify({
    section_id: section.section_id,
    order: section.order,
    data: section.data || {},
    mapping_items: section.mapping_items || {},
    ...pickPersistedExtrasFromSection(section),
  });

  const buildEmptyMappingItem = (fields) => {
    const item = {};
    (fields || []).forEach((f) => { item[f.name] = f.type === 'checkbox' ? false : ''; });
    return item;
  };

  const addSection = (sectionId) => {
    const selectedSection = availableSections.find(s => s.id == sectionId);
    if (!selectedSection) { toast.error("Section not found"); return; }
    const newSection = {
      section_id: parseInt(sectionId),
      order: sections.length,
      data: {},
      mapping_items: {},
      isCollapsed: false,
      files: {},
      mapping_files: {},
      isNew: true,
      dirty: true,
      originalFingerprint: null
    };
    if (selectedSection.fields_config) {
      selectedSection.fields_config.forEach(fc => {
        newSection.data[fc.name] = fc.type === 'checkbox' ? false : '';
      });
    }
    setSections(prev => [...prev, newSection]);
    setShowComponentPicker(false);
    setSearchTerm('');
    setCurrentPage(1);
    toast.success(`${selectedSection.name} added`);
  };

  const showDeleteModal = (sectionIndex) => {
    setDeleteTarget(sectionIndex);
    showModal();
  };

  const cancelDelete = () => {
    setDeleteTarget(null);
    hideModal();
  };

  const confirmDelete = () => {
    if (deleteTarget === null) return;
    setProcessingDelete(true);
    // Detail sections are stored inside JSON, so deleting does not need immediate DB trigger.
    // It is removed from UI state and saved when clicking "Save changes"
    removeSectionFromState(deleteTarget);
    toast.success("Removed");
    setProcessingDelete(false);
    hideModal();
    setDeleteTarget(null);
  };

  const removeSectionFromState = (index) => {
    const updated = sections.filter((_, i) => i !== index).map((s, i) => ({ ...s, order: i }));
    setSections(updated);
    const newPreviews = { ...filePreviews };
    Object.keys(filePreviews).forEach(k => { if (k.startsWith(`${index}_`)) delete newPreviews[k]; });
    setFilePreviews(newPreviews);
  };

  const updateSectionData = (si, fn, val) => {
    setSections(prev =>
      prev.map((s, i) =>
        i !== si ? s : {
          ...s,
          data: { ...s.data, [fn]: val },
          dirty: true,
        }
      )
    );
  };

  const updateMappingItem = (si, groupName, ii, fn, val) => {
    setSections(prev =>
      prev.map((s, i) => {
        if (i !== si) return s;
        const groupItems = s.mapping_items?.[groupName] ?? [];
        return {
          ...s,
          mapping_items: {
            ...s.mapping_items,
            [groupName]: groupItems.map((item, j) =>
              j !== ii ? item : { ...item, [fn]: val }
            ),
          },
          dirty: true,
        };
      })
    );
  };

  const updateNestedMappingItem = (si, parentGroup, parentIndex, nestedGroup, nestedIndex, fn, val) => {
    setSections(prev =>
      prev.map((s, i) => {
        if (i !== si) return s;
        const parentItems = s.mapping_items?.[parentGroup] ?? [];
        return {
          ...s,
          mapping_items: {
            ...s.mapping_items,
            [parentGroup]: parentItems.map((row, j) => {
              if (j !== parentIndex) return row;
              const nestedItems = Array.isArray(row[nestedGroup]) ? row[nestedGroup] : [];
              return {
                ...row,
                [nestedGroup]: nestedItems.map((cell, k) =>
                  k !== nestedIndex ? cell : { ...cell, [fn]: val }
                ),
              };
            }),
          },
          dirty: true,
        };
      })
    );
  };

  const addMappingItem = (si, groupName) => {
    const ns = [...sections];
    if (!ns[si]) return;
    const sel = getSelectedSection(ns[si].section_id);
    const groups = normalizeMappingConfig(sel?.mapping_config || []);
    const group = groups.find((g) => g.group_name === groupName) || groups[0];
    if (!group) return;
    const newItem = buildEmptyMappingItem(group.fields || []);
    getChildMappingGroups(groups, groupName).forEach((cg) => {
      newItem[cg.group_name] = [];
    });
    if (!ns[si].mapping_items) ns[si].mapping_items = {};
    if (!ns[si].mapping_items[groupName]) ns[si].mapping_items[groupName] = [];
    ns[si].mapping_items[groupName].push(newItem);
    ns[si].dirty = true;
    setSections(ns);
  };

  const addNestedMappingItem = (si, parentGroup, parentIndex, nestedGroupName) => {
    const ns = [...sections];
    if (!ns[si]) return;
    const sel = getSelectedSection(ns[si].section_id);
    const groups = normalizeMappingConfig(sel?.mapping_config || []);
    const child = groups.find((g) => g.group_name === nestedGroupName && g.parent_group === parentGroup);
    if (!child) return;
    const newNested = buildEmptyMappingItem(child.fields || []);
    if (!ns[si].mapping_items?.[parentGroup]?.[parentIndex]) return;
    const row = ns[si].mapping_items[parentGroup][parentIndex];
    if (!Array.isArray(row[nestedGroupName])) row[nestedGroupName] = [];
    row[nestedGroupName].push(newNested);
    ns[si].dirty = true;
    setSections(ns);
  };

  const removeNestedMappingItem = (si, parentGroup, parentIndex, nestedGroup, nestedIndex) => {
    const ns = [...sections];
    if (!ns[si]?.mapping_items?.[parentGroup]?.[parentIndex]?.[nestedGroup]) return;
    ns[si].mapping_items[parentGroup][parentIndex][nestedGroup].splice(nestedIndex, 1);
    if (ns[si].mapping_files?.[parentGroup]?.[parentIndex]?.[nestedGroup]) {
      delete ns[si].mapping_files[parentGroup][parentIndex][nestedGroup][nestedIndex];
    }
    const pkPrefix = `${si}_mapping_${parentGroup}_${parentIndex}_${nestedGroup}_${nestedIndex}`;
    setFilePreviews((prev) => {
      const np = { ...prev };
      Object.keys(np).forEach((k) => {
        if (k.startsWith(pkPrefix)) {
          if (np[k]?.startsWith('blob:')) URL.revokeObjectURL(np[k]);
          delete np[k];
        }
      });
      return np;
    });
    ns[si].dirty = true;
    setSections(ns);
  };

  const removeMappingItem = (si, groupName, ii) => {
    const ns = [...sections];
    if (!ns[si]?.mapping_items?.[groupName]) return;
    ns[si].mapping_items[groupName].splice(ii, 1);
    ns[si].dirty = true;
    setSections(ns);
  };

  const handleFileUpload = (si, fn, file, isMapping = false, mapCtx = null) => {
    const ns = [...sections];
    if (!ns[si]) return;
    if (isMapping && mapCtx?.groupName != null && mapCtx.itemIndex != null) {
      const { groupName, itemIndex, nestedGroup, nestedItemIndex } = mapCtx;
      if (!ns[si].mapping_files) ns[si].mapping_files = {};
      if (!ns[si].mapping_files[groupName]) ns[si].mapping_files[groupName] = {};
      if (!ns[si].mapping_files[groupName][itemIndex]) ns[si].mapping_files[groupName][itemIndex] = {};
      if (nestedGroup != null && nestedItemIndex != null) {
        if (!ns[si].mapping_files[groupName][itemIndex][nestedGroup]) ns[si].mapping_files[groupName][itemIndex][nestedGroup] = {};
        if (!ns[si].mapping_files[groupName][itemIndex][nestedGroup][nestedItemIndex]) {
          ns[si].mapping_files[groupName][itemIndex][nestedGroup][nestedItemIndex] = {};
        }
        ns[si].mapping_files[groupName][itemIndex][nestedGroup][nestedItemIndex][fn] = file;
        if (!ns[si].mapping_items?.[groupName]?.[itemIndex]) {
          ns[si].dirty = true;
          setSections(ns);
          return;
        }
        if (!Array.isArray(ns[si].mapping_items[groupName][itemIndex][nestedGroup])) {
          ns[si].mapping_items[groupName][itemIndex][nestedGroup] = [];
        }
        if (!ns[si].mapping_items[groupName][itemIndex][nestedGroup][nestedItemIndex]) {
          ns[si].mapping_items[groupName][itemIndex][nestedGroup][nestedItemIndex] = {};
        }
        ns[si].mapping_items[groupName][itemIndex][nestedGroup][nestedItemIndex][fn] = file.name;
      } else {
        ns[si].mapping_files[groupName][itemIndex][fn] = file;
        if (!ns[si].mapping_items) ns[si].mapping_items = {};
        if (!ns[si].mapping_items[groupName]) ns[si].mapping_items[groupName] = [];
        if (!ns[si].mapping_items[groupName][itemIndex]) ns[si].mapping_items[groupName][itemIndex] = {};
        ns[si].mapping_items[groupName][itemIndex][fn] = file.name;
      }
    } else {
      ns[si].files[fn] = file;
      ns[si].data[fn] = file.name;
    }
    if (file.type.startsWith('image/')) {
      const previewUrl = URL.createObjectURL(file);
      const pk = isMapping && mapCtx?.groupName != null && mapCtx.itemIndex != null
        ? (mapCtx.nestedGroup != null && mapCtx.nestedItemIndex != null
          ? `${si}_mapping_${mapCtx.groupName}_${mapCtx.itemIndex}_${mapCtx.nestedGroup}_${mapCtx.nestedItemIndex}_${fn}`
          : `${si}_mapping_${mapCtx.groupName}_${mapCtx.itemIndex}_${fn}`)
        : `${si}_${fn}`;
      setFilePreviews(prev => ({ ...prev, [pk]: previewUrl }));
    }
    ns[si].dirty = true;
    setSections(ns);
  };

  const removeFile = (si, fn, isMapping = false, mapCtx = null) => {
    const ns = [...sections];
    if (!ns[si]) return;
    if (isMapping && mapCtx?.groupName != null && mapCtx.itemIndex != null) {
      const { groupName, itemIndex, nestedGroup, nestedItemIndex } = mapCtx;
      if (nestedGroup != null && nestedItemIndex != null) {
        if (ns[si].mapping_files?.[groupName]?.[itemIndex]?.[nestedGroup]?.[nestedItemIndex]) {
          delete ns[si].mapping_files[groupName][itemIndex][nestedGroup][nestedItemIndex][fn];
        }
        if (ns[si].mapping_items?.[groupName]?.[itemIndex]?.[nestedGroup]?.[nestedItemIndex]) {
          ns[si].mapping_items[groupName][itemIndex][nestedGroup][nestedItemIndex][fn] = '';
        }
      } else {
        if (ns[si].mapping_files?.[groupName]?.[itemIndex]) delete ns[si].mapping_files[groupName][itemIndex][fn];
        if (ns[si].mapping_items?.[groupName]?.[itemIndex]) ns[si].mapping_items[groupName][itemIndex][fn] = '';
      }
    } else {
      delete ns[si].files[fn];
      ns[si].data[fn] = '';
    }
    const pk = isMapping && mapCtx?.groupName != null && mapCtx.itemIndex != null
      ? (mapCtx.nestedGroup != null && mapCtx.nestedItemIndex != null
        ? `${si}_mapping_${mapCtx.groupName}_${mapCtx.itemIndex}_${mapCtx.nestedGroup}_${mapCtx.nestedItemIndex}_${fn}`
        : `${si}_mapping_${mapCtx.groupName}_${mapCtx.itemIndex}_${fn}`)
      : `${si}_${fn}`;
    setFilePreviews((prev) => {
      if (prev[pk]?.startsWith('blob:')) URL.revokeObjectURL(prev[pk]);
      const np = { ...prev };
      delete np[pk];
      return np;
    });
    ns[si].dirty = true;
    setSections(ns);
  };

  const toggleSectionCollapse = (index) => {
    const ns = [...sections];
    if (!ns[index]) return;
    ns[index].isCollapsed = !ns[index].isCollapsed;
    setSections(ns);
  };

  const handleSectionDragStart = (e, index) => {
    if (!isDraggable) return;
    setDraggedSection(index);
    e.dataTransfer.effectAllowed = 'move';
  };
  const handleSectionDragOver = (e) => { if (!isDraggable) return; e.preventDefault(); };
  const handleSectionDrop = (e, dropIndex) => {
    if (!isDraggable || draggedSection === null) return;
    e.preventDefault();
    const ns = [...sections];
    const [removed] = ns.splice(draggedSection, 1);
    ns.splice(dropIndex, 0, removed);
    setSections(ns.map((s, i) => ({ ...s, order: i, dirty: s.order !== i ? true : s.dirty })));
    setDraggedSection(null);
  };

  const getSelectedSection = (id) => availableSections.find(s => s.id == id);

  const renderFieldInput = (field, value, onChange, placeholder, required, si, fn, isMapping = false, mapCtx = null) => {
    const groupName = isMapping ? (mapCtx?.groupName || 'items') : null;
    const itemIndex = isMapping ? (mapCtx?.itemIndex ?? null) : null;
    const nestedGroup = isMapping ? (mapCtx?.nestedGroup ?? null) : null;
    const nestedItemIndex = isMapping ? (mapCtx?.nestedItemIndex ?? null) : null;
    const pk = isMapping
      ? (nestedGroup != null && nestedItemIndex != null
        ? `${si}_mapping_${groupName}_${itemIndex}_${nestedGroup}_${nestedItemIndex}_${fn}`
        : `${si}_mapping_${groupName}_${itemIndex}_${fn}`)
      : `${si}_${fn}`;
    const nestedFileLeaf = isMapping && nestedGroup != null && nestedItemIndex != null
      ? sections[si]?.mapping_files?.[groupName]?.[itemIndex]?.[nestedGroup]?.[nestedItemIndex]?.[fn]
      : null;
    const hasFile = isMapping
      ? (nestedFileLeaf || (!nestedGroup && sections[si]?.mapping_files?.[groupName]?.[itemIndex]?.[fn]) || filePreviews[pk])
      : (sections[si]?.files?.[fn] || filePreviews[pk]);
    const fileValue = isMapping
      ? (nestedGroup != null && nestedItemIndex != null
        ? nestedFileLeaf
        : sections[si]?.mapping_files?.[groupName]?.[itemIndex]?.[fn])
      : sections[si]?.files?.[fn];
    const fileMapCtx = isMapping && groupName != null && itemIndex != null
      ? { groupName, itemIndex, nestedGroup, nestedItemIndex }
      : null;

    switch (field.type) {
      case 'code':
        return (
          <div className="sb-code-wrap">
            <WisyswigEditor value={value || ''} onChange={onChange} language={field.language || 'html'} height="400px" />
          </div>
        );
      case 'textarea':
        return (
          <textarea className="sb-input" value={value || ''} onChange={e => onChange(e.target.value)}
            rows="4" required={required} placeholder={placeholder} style={{ resize: 'vertical', minHeight: 80 }} />
        );
      case 'select':
        return (
          <select className="sb-input" value={value || ''} onChange={e => onChange(e.target.value)} required={required}>
            <option value="">Choose an option…</option>
            {field.options?.map((opt, i) => (
              <option key={i} value={opt.value || opt}>{opt.label || opt}</option>
            ))}
          </select>
        );
      case 'checkbox':
        return (
          <label className="sb-checkbox-label">
            <input type="checkbox" className="sb-checkbox" checked={value || false}
              onChange={e => onChange(e.target.checked)} required={required} />
            <span className="sb-checkbox-box" />
            <span className="sb-checkbox-text">{field.label}</span>
          </label>
        );
      case 'radio':
        return (
          <div className="sb-radio-group">
            {field.options?.map((opt, i) => (
              <label key={i} className="sb-radio-label">
                <input type="radio" className="sb-radio"
                  name={`${si}_${fn}${isMapping ? `_m_${groupName}_${itemIndex}${nestedGroup != null ? `_n_${nestedGroup}_${nestedItemIndex}` : ''}` : ''}`}
                  value={opt.value || opt}
                  checked={value === (opt.value || opt)}
                  onChange={e => onChange(e.target.value)} required={required} />
                <span className="sb-radio-dot" />
                <span>{opt.label || opt}</span>
              </label>
            ))}
          </div>
        );
      case 'color':
        return (
          <div className="sb-color-wrap">
            <input type="color" className="sb-color-swatch" value={value || '#3b82f6'}
              onChange={e => onChange(e.target.value)} />
            <input type="text" className="sb-input" value={value || '#3b82f6'}
              onChange={e => onChange(e.target.value)} placeholder={placeholder} />
          </div>
        );
      case 'image':
        return (
          <div>
            {hasFile && (
              <div className="sb-file-preview">
                <div className="sb-file-preview-info">
                  {fileValue ? (fileValue.type.startsWith('image/') ? <ImageIcon size={14} /> : <File size={14} />) : <ImageIcon size={14} />}
                  <span>{fileValue ? fileValue.name : 'Existing file'}</span>
                </div>
                <button type="button" className="sb-btn-icon-danger" onClick={() => removeFile(si, fn, isMapping, fileMapCtx)}>
                  <Trash2 size={12} />
                </button>
                {filePreviews[pk] && (
                  <div className="sb-file-thumb">
                    <img src={filePreviews[pk]} alt="Preview" onError={e => e.target.style.display = 'none'} />
                  </div>
                )}
              </div>
            )}
            <label className="sb-file-input-label" htmlFor={`file-${si}-${fn}${isMapping ? `-m-${groupName}-${itemIndex}${nestedGroup != null ? `-n-${nestedGroup}-${nestedItemIndex}` : ''}` : ''}`}>
              <Upload size={14} />
              <span>{hasFile ? 'Replace file' : 'Choose file'}</span>
              <input type="file" id={`file-${si}-${fn}${isMapping ? `-m-${groupName}-${itemIndex}${nestedGroup != null ? `-n-${nestedGroup}-${nestedItemIndex}` : ''}` : ''}`}
                accept={
                  field.accept ||
                  'image/png,image/jpeg,image/jpg,image/gif,image/svg+xml,image/webp'
                }
                onChange={e => { const f = e.target.files?.[0]; if (f) handleFileUpload(si, fn, f, isMapping, fileMapCtx); }}
                style={{ display: 'none' }} />
            </label>
            <p className="sb-file-hint">JPG, PNG, GIF, SVG, WebP — max 3 MB</p>
          </div>
        );
      case 'file':
        return (
          <div>
            {hasFile && (
              <div className="sb-file-preview">
                <div className="sb-file-preview-info">
                  {fileValue ? (fileValue.type.startsWith('image/') ? <ImageIcon size={14} /> : <File size={14} />) : <ImageIcon size={14} />}
                  <span>{fileValue ? fileValue.name : 'Existing file'}</span>
                </div>
                <button type="button" className="sb-btn-icon-danger" onClick={() => removeFile(si, fn, isMapping, fileMapCtx)}>
                  <Trash2 size={12} />
                </button>
                {filePreviews[pk] && (
                  <div className="sb-file-thumb">
                    <img src={filePreviews[pk]} alt="Preview" onError={e => e.target.style.display = 'none'} />
                  </div>
                )}
              </div>
            )}
            <label className="sb-file-input-label" htmlFor={`file-${si}-${fn}${isMapping ? `-m-${groupName}-${itemIndex}${nestedGroup != null ? `-n-${nestedGroup}-${nestedItemIndex}` : ''}` : ''}`}>
              <Upload size={14} />
              <span>{hasFile ? 'Replace file' : 'Choose file'}</span>
              <input type="file" id={`file-${si}-${fn}${isMapping ? `-m-${groupName}-${itemIndex}${nestedGroup != null ? `-n-${nestedGroup}-${nestedItemIndex}` : ''}` : ''}`}
                accept={
                  field.accept ||
                  'image/png,image/jpeg,image/jpg,image/gif,image/svg+xml,image/webp,video/mp4,video/quicktime,video/x-msvideo,application/pdf,.pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,.doc,.docx'
                }
                onChange={e => { const f = e.target.files?.[0]; if (f) handleFileUpload(si, fn, f, isMapping, fileMapCtx); }}
                style={{ display: 'none' }} />
            </label>
            <p className="sb-file-hint">Images, video, PDF, Word — max 3 MB</p>
          </div>
        );
      default:
        return (
          <input type={field.type} className="sb-input" value={value || ''}
            onChange={e => onChange(e.target.value)} required={required} placeholder={placeholder} />
        );
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!entry?.id) { toast.error("Entry not found"); return; }
    
    // For module entries stored in JSON, we must send ALL sections currently in the list to preserve the whole list and order!
    const sectionsToSave = sections.filter((s) => s.section_id);
    
    setIsSaving(true);
    const formData = new FormData();
    formData.append('entry_id', entry.id);
    
    sectionsToSave.forEach((section, index) => {
      if (!section.section_id) return;
      formData.append(`sections[${index}][section_id]`, section.section_id);
      formData.append(`sections[${index}][order]`, section.order);
      formData.append(`sections[${index}][section_data]`, JSON.stringify(buildSectionDataJsonPayload(section)));
      
      if (section.files) {
        Object.entries(section.files).forEach(([fn, file]) => {
          if (isFileObject(file)) formData.append(`sections[${index}][files][${fn}]`, file);
        });
      }
      if (section.mapping_files) {
        const appendMappingFileLeaves = (branch, pathParts) => {
          if (!branch || typeof branch !== 'object') return;
          Object.entries(branch).forEach(([k, val]) => {
            if (isFileObject(val)) {
              const tail = [...pathParts, k].map((p) => `[${p}]`).join('');
              formData.append(`sections[${index}][mapping_files]${tail}`, val);
            } else if (Array.isArray(val)) {
              val.forEach((item, i) => {
                if (item && typeof item === 'object') {
                  appendMappingFileLeaves(item, [...pathParts, k, String(i)]);
                }
              });
            } else if (val && typeof val === 'object') {
              appendMappingFileLeaves(val, [...pathParts, k]);
            }
          });
        };
        appendMappingFileLeaves(section.mapping_files, []);
      }
    });
    
    router.post(getSectionStoreRoute(), formData, {
      preserveScroll: true,
      preserveState: false, // forces Inertia to reload the formatted activeSections including new uploaded asset URLs!
      onSuccess: () => {
        setIsSaving(false);
        toast.success("Sections saved successfully!");
      },
      onError: (errors) => {
        setIsSaving(false);
        toast.error("Error saving: " + (errors.message || 'Unknown error'));
      }
    });
  };

  useCtrlSSubmit(handleSubmit, !isSaving);

  useEffect(() => {
    return () => { Object.values(filePreviews).forEach(url => { if (url.startsWith('blob:')) URL.revokeObjectURL(url); }); };
  }, [filePreviews]);

  return (
    <>
      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap');
        .sb-bulk { margin-bottom: 14px; border: 1px solid var(--sb-border, #e5e7eb); border-radius: 8px; background: var(--sb-surface-2, #f9fafb); padding: 0 10px 10px; }
        .sb-bulk > summary { cursor: pointer; list-style: none; font-size: 0.82rem; font-weight: 600; color: var(--sb-text, #374151); padding: 8px 0; display: flex; align-items: center; gap: 6px; user-select: none; }
        .sb-bulk > summary::-webkit-details-marker { display: none; }
        .sb-bulk-hint { font-size: 0.72rem; color: var(--sb-text-subtle, #6b7280); margin: 0 0 8px; line-height: 1.45; }
        .sb-bulk-textarea { width: 100%; min-height: 100px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.72rem; padding: 8px; border-radius: 6px; border: 1px solid var(--sb-border, #e5e7eb); resize: vertical; box-sizing: border-box; }
        .sb-bulk-textarea.sb-bulk-json { min-height: 180px; }
        .sb-bulk-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
      `}</style>

      {/* Delete modal */}
      <DeleteConfirmationModal
        modalRef={modalRef}
        title="Confirm Deletion"
        message="Are you sure you want to remove this section?"
        itemName={deleteTarget !== null ? getSelectedSection(sections[deleteTarget]?.section_id)?.name : null}
        onConfirm={confirmDelete}
        onCancel={cancelDelete}
        processing={processingDelete}
      />

      <ToastContainer position="top-right" autoClose={3000} hideProgressBar newestOnTop closeOnClick />

      <div className="sb-wrap">
        {/* Page Header */}
        <div className="sb-page-header">
          <div>
            {backUrl && (
              <Link href={backUrl} className="btn btn-sm btn-outline-secondary mb-2">
                ← Back to entry list
              </Link>
            )}
            <h1 className="text-muted mb-1">{module?.name || 'Entry Detail'}</h1>
            <p className="text-muted mb-0 flex items-center">
              Entry:&nbsp;
              <span>{entryLabel || 'Untitled Entry'}</span>
            </p>
          </div>
          <button className="sb-drag-toggle" onClick={() => setIsDraggable(!isDraggable)}>
            <GripVertical size={14} />
            Drag & Drop
            <span className={`sb-toggle-pill ${isDraggable ? 'on' : ''}`} />
          </button>
        </div>

        {/* Stats */}
        <div className="sb-stats">
          <span className="sb-stats-badge">{sections.length} {sections.length === 1 ? 'section' : 'sections'}</span>
          <span>mapped to this module entry</span>
        </div>

        <form onSubmit={handleSubmit}>
          {/* Section Cards */}
          {sections.map((section, sIndex) => {
            const selectedSection = getSelectedSection(section.section_id);
            const fields = selectedSection?.fields_config || [];
            const hasMapping = selectedSection?.mapping_enabled && selectedSection?.mapping_config;

            return (
              <div
                key={sIndex}
                className={`sb-section-card ${section.isCollapsed ? 'sb-collapsed' : ''} ${draggedSection === sIndex ? 'sb-dragging' : ''}`}
                draggable={isDraggable}
                onDragStart={e => handleSectionDragStart(e, sIndex)}
                onDragOver={handleSectionDragOver}
                onDrop={e => handleSectionDrop(e, sIndex)}
              >
                <div className="sb-section-header" onClick={() => toggleSectionCollapse(sIndex)}>
                  <div className="sb-section-header-left">
                    {isDraggable && <GripVertical size={15} className="sb-drag-grip" onClick={e => e.stopPropagation()} />}
                    <span className="sb-collapse-btn" onClick={e => { e.stopPropagation(); toggleSectionCollapse(sIndex); }}>
                      {section.isCollapsed ? <ChevronDown size={15} /> : <ChevronUp size={15} />}
                    </span>
                    <div className="sb-section-icon"><LayoutTemplate size={14} /></div>
                    <span className="sb-section-name">{selectedSection?.name || 'Unknown Section'}</span>
                    <div className="sb-badges">
                      <span className="sb-badge sb-badge-num">#{sIndex + 1}</span>
                      {hasMapping && <span className="sb-badge sb-badge-repeat"><Repeat size={9} style={{ verticalAlign: 'middle', marginRight: 2 }} />Repeatable</span>}
                      {section.pivot_id && <span className="sb-badge sb-badge-saved">Saved</span>}
                    </div>
                  </div>
                  <button type="button" className="sb-btn-delete" onClick={e => { e.stopPropagation(); showDeleteModal(sIndex); }}>
                    <Trash2 size={14} />
                  </button>
                </div>

                {!section.isCollapsed && selectedSection && (
                  <div className="sb-section-body">
                    <div className="sb-fields-grid">
                      {fields.length > 0 ? fields.map((field, fIndex) => {
                        const fieldName = field.name;
                        const currentValue = section.data[fieldName] || '';
                        const isFullWidth = field.type === 'code' || field.type === 'textarea';
                        return (
                          <div key={fIndex} className={`sb-field-group ${isFullWidth ? 'sb-field-full' : ''}`}>
                            <label className="sb-label">
                              {fieldTypeIcons[field.type] || <Type size={13} />}
                              {field.label}
                              {field.required && <span className="sb-required">*</span>}
                            </label>
                            {renderFieldInput(field, currentValue, v => updateSectionData(sIndex, fieldName, v), field.placeholder || `Enter ${field.label.toLowerCase()}`, field.required, sIndex, fieldName)}
                          </div>
                        );
                      }) : (
                        <div className="sb-empty-fields">
                          <Boxes size={22} style={{ color: 'var(--sb-text-subtle)', marginBottom: 8 }} />
                          <p style={{ margin: 0, color: 'var(--sb-text-subtle)', fontSize: '0.8rem' }}>
                            Static section — no configurable fields.
                          </p>
                        </div>
                      )}

                      {hasMapping && selectedSection.mapping_config && (
                        <>
                          {normalizeMappingConfig(selectedSection.mapping_config || [])
                            .filter(isTopLevelMappingGroup)
                            .map((group) => {
                            const groupName = group.group_name || 'items';
                            const items = section.mapping_items?.[groupName] || [];
                            const mappingGroupsAll = normalizeMappingConfig(selectedSection.mapping_config || []);
                            return (
                              <div key={groupName} className="sb-mapping-section">
                                <div className="sb-mapping-header">
                                  <div>
                                    <p className="sb-mapping-title">
                                      <Boxes size={15} />
                                      {group.group_label || 'Repeatable Items'}
                                      <span className="sb-badge sb-badge-num">{items.length}</span>
                                    </p>
                                    <p className="sb-mapping-hint">
                                      Use {'{item.field_name}'} inside <code>{'<!-- START REPEATABLE '}{groupName}{' -->'}</code>
                                    </p>
                                  </div>
                                  <button type="button" className="sb-btn-primary sb-btn-sm" onClick={() => addMappingItem(sIndex, groupName)}>
                                    <Plus size={13} /> Add Item
                                  </button>
                                </div>

                                {items.length === 0 ? (
                                  <div className="sb-empty-fields" style={{ marginTop: 4 }}>
                                    <p style={{ margin: 0, fontSize: '0.8rem' }}>No items yet — click "Add Item" to begin.</p>
                                  </div>
                                ) : (
                                  items.map((item, itemIndex) => (
                                    <div key={itemIndex} className="sb-mapping-item">
                                      <div className="sb-mapping-item-header">
                                        <span className="sb-mapping-item-title">
                                          <Repeat size={12} /> Item {itemIndex + 1}
                                          <span className="sb-badge sb-badge-num">#{itemIndex + 1}</span>
                                        </span>
                                        <button type="button" className="sb-btn-icon-danger" onClick={() => removeMappingItem(sIndex, groupName, itemIndex)}>
                                          <Trash2 size={12} />
                                        </button>
                                      </div>
                                      <div className="sb-mapping-item-body">
                                        <div className="sb-fields-grid">
                                          {(group.fields || []).map((field, fi) => (
                                            <div key={fi} className={`sb-field-group ${field.type === 'code' ? 'sb-field-full' : ''}`}>
                                              <label className="sb-label">
                                                {fieldTypeIcons[field.type] || <Type size={13} />}
                                                {field.label}
                                                {field.required && <span className="sb-required">*</span>}
                                              </label>
                                              {renderFieldInput(
                                                field,
                                                item?.[field.name] || '',
                                                v => updateMappingItem(sIndex, groupName, itemIndex, field.name, v),
                                                field.placeholder || `Enter ${field.label.toLowerCase()}`,
                                                field.required,
                                                sIndex,
                                                field.name,
                                                true,
                                                { groupName, itemIndex }
                                              )}
                                            </div>
                                          ))}
                                        </div>
                                        {getChildMappingGroups(mappingGroupsAll, groupName).map((child) => {
                                          const nestedName = child.group_name || 'items';
                                          const nestedItems = Array.isArray(item?.[nestedName]) ? item[nestedName] : [];
                                          return (
                                            <div key={nestedName} className="sb-mapping-nested" style={{ marginTop: 12, paddingLeft: 12, borderLeft: '2px solid var(--sb-border, #e5e7eb)' }}>
                                              <div className="sb-mapping-header" style={{ marginBottom: 8 }}>
                                                <div>
                                                  <p className="sb-mapping-title" style={{ fontSize: '0.85rem' }}>
                                                    <Layers size={14} />
                                                    {child.group_label || nestedName}
                                                    <span className="sb-badge sb-badge-num">{nestedItems.length}</span>
                                                  </p>
                                                  <p className="sb-mapping-hint" style={{ fontSize: '0.75rem' }}>
                                                    Inside this row: <code>{'<!-- START REPEATABLE '}{nestedName}{' -->'}</code>
                                                  </p>
                                                </div>
                                                <button
                                                  type="button"
                                                  className="sb-btn-primary sb-btn-sm"
                                                  onClick={() => addNestedMappingItem(sIndex, groupName, itemIndex, nestedName)}
                                                >
                                                  <Plus size={13} /> Add cell
                                                </button>
                                              </div>
                                              {nestedItems.length === 0 ? (
                                                <p style={{ margin: 0, fontSize: '0.8rem', color: 'var(--sb-text-subtle)' }}>No cells yet — add one for each column in this row.</p>
                                              ) : (
                                                nestedItems.map((nItem, ni) => (
                                                  <div key={ni} className="sb-mapping-item" style={{ marginTop: 8 }}>
                                                    <div className="sb-mapping-item-header">
                                                      <span className="sb-mapping-item-title">
                                                        <Repeat size={12} /> Cell {ni + 1}
                                                      </span>
                                                      <button
                                                        type="button"
                                                        className="sb-btn-icon-danger"
                                                        onClick={() => removeNestedMappingItem(sIndex, groupName, itemIndex, nestedName, ni)}
                                                      >
                                                        <Trash2 size={12} />
                                                      </button>
                                                    </div>
                                                    <div className="sb-mapping-item-body">
                                                      <div className="sb-fields-grid">
                                                        {(child.fields || []).map((field, fi2) => (
                                                          <div key={fi2} className={`sb-field-group ${field.type === 'code' ? 'sb-field-full' : ''}`}>
                                                            <label className="sb-label">
                                                              {fieldTypeIcons[field.type] || <Type size={13} />}
                                                              {field.label}
                                                              {field.required && <span className="sb-required">*</span>}
                                                            </label>
                                                            {renderFieldInput(
                                                              field,
                                                              nItem?.[field.name] || '',
                                                              v => updateNestedMappingItem(sIndex, groupName, itemIndex, nestedName, ni, field.name, v),
                                                              field.placeholder || `Enter ${field.label.toLowerCase()}`,
                                                              field.required,
                                                              sIndex,
                                                              field.name,
                                                              true,
                                                              { groupName, itemIndex, nestedGroup: nestedName, nestedItemIndex: ni }
                                                            )}
                                                          </div>
                                                        ))}
                                                      </div>
                                                    </div>
                                                  </div>
                                                ))
                                              )}
                                            </div>
                                          );
                                        })}
                                      </div>
                                    </div>
                                  ))
                                )}
                              </div>
                            );
                          })}
                        </>
                      )}
                    </div>
                  </div>
                )}

                {!section.isCollapsed && !selectedSection && (
                  <div className="sb-section-body">
                    <div className="sb-warn">Section template not found. Please select a valid section.</div>
                  </div>
                )}
              </div>
            );
          })}

          {/* Component Picker */}
          {(showComponentPicker || sections.length === 0) && (
            <div className="sb-picker">
              <div className="sb-picker-head">
                <div className="sb-picker-head-row">
                  <h6 className="sb-picker-title">
                    <Blocks size={16} /> Section Library
                    <span className="sb-badge sb-badge-num">{availableSections.length}</span>
                  </h6>
                  {sections.length > 0 && (
                    <button type="button" className="sb-btn-ghost" onClick={() => setShowComponentPicker(false)}>
                      <X size={16} />
                    </button>
                  )}
                </div>
                <div className="sb-search-wrap">
                  <Search size={14} className="sb-search-icon" />
                  <input
                    ref={searchInputRef}
                    type="text"
                    className="sb-search-input"
                    placeholder="Search by name or identifier…"
                    value={searchTerm}
                    onChange={e => { setSearchTerm(e.target.value); setCurrentPage(1); }}
                  />
                  {searchTerm && (
                    <button type="button" className="sb-search-clear" onClick={() => { setSearchTerm(''); searchInputRef.current?.focus(); }}>
                      <X size={13} />
                    </button>
                  )}
                </div>
                <p className="sb-picker-meta">Showing {filteredSections.length} of {availableSections.length} sections</p>
              </div>

              <div className="sb-picker-body">
                {filteredSections.length === 0 ? (
                  <div className="sb-picker-empty">
                    <div className="sb-picker-empty-icon"><Blocks size={36} /></div>
                    <p style={{ fontWeight: 600, marginBottom: 4 }}>No sections found</p>
                    <p style={{ fontSize: '0.8rem', color: 'var(--sb-text-subtle)', marginBottom: 12 }}>
                      {searchTerm ? `Nothing matches "${searchTerm}"` : 'No sections available'}
                    </p>
                    {searchTerm && (
                      <button type="button" className="sb-btn-outline sb-btn-sm" onClick={() => setSearchTerm('')}>Clear search</button>
                    )}
                  </div>
                ) : (
                  <>
                    <p className="sb-picker-prompt">
                      {sections.length === 0 ? 'Pick your first section to get started' : 'Choose a section to add'}
                    </p>
                    <div className="sb-comp-grid">
                      {paginatedSections.map(section => (
                        <button key={section.id} type="button" className="sb-comp-btn" onClick={() => addSection(section.id)} title={`${section.name} (${section.identifier})`}>
                          <div className="sb-comp-icon"><LayoutTemplate size={22} /></div>
                          <div className="sb-comp-name">{section.name}</div>
                          <div className="sb-comp-id">{section.identifier}</div>
                          <span className={`sb-comp-tag ${section.mapping_enabled ? 'sb-comp-tag-repeat' : 'sb-comp-tag-regular'}`}>
                            {section.mapping_enabled ? 'Repeatable' : 'Regular'}
                          </span>
                        </button>
                      ))}
                    </div>

                    {totalPages > 1 && (
                      <>
                        <div className="sb-pagination">
                          <button className="sb-page-btn" onClick={() => setCurrentPage(p => p - 1)} disabled={currentPage === 1}>‹ Prev</button>
                          {Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
                            let pn;
                            if (totalPages <= 5) pn = i + 1;
                            else if (currentPage <= 3) pn = i + 1;
                            else if (currentPage >= totalPages - 2) pn = totalPages - 4 + i;
                            else pn = currentPage - 2 + i;
                            return (
                              <button key={pn} className={`sb-page-btn ${currentPage === pn ? 'active' : ''}`} onClick={() => setCurrentPage(pn)}>{pn}</button>
                            );
                          })}
                          <button className="sb-page-btn" onClick={() => setCurrentPage(p => p + 1)} disabled={currentPage === totalPages}>Next ›</button>
                        </div>
                        <p className="sb-pagination-meta">Page {currentPage} of {totalPages} · {startIndex + 1}–{Math.min(startIndex + itemsPerPage, filteredSections.length)} of {filteredSections.length}</p>
                      </>
                    )}
                  </>
                )}
              </div>
            </div>
          )}

          {/* Add More */}
          {sections.length > 0 && !showComponentPicker && (
            <div className="sb-add-section-row">
              <button type="button" className="sb-btn-add" onClick={() => setShowComponentPicker(true)}>
                <Plus size={15} /> Add another section
              </button>
            </div>
          )}

          {/* Save */}
          {sections.length > 0 && (
            <div className="sb-save-bar">
              <button type="submit" className="sb-btn-primary" disabled={isSaving}>
                {isSaving ? <><div className="sb-spinner" /> Saving…</> : <><Save size={14} /> Save changes</>}
              </button>
            </div>
          )}
        </form>
      </div>
    </>
  );
};

export default Detail;
