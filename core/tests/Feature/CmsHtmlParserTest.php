<?php

use App\Services\CmsHtmlParser;

it('parses html attributes into fields config, mapping config, and defaults', function () {
    $parser = new CmsHtmlParser();

    $html = <<<HTML
<div>
    <h1 data-text="title">My title</h1>
    <p data-textarea="description">This is a description.</p>
    <img data-image="hero_image" src="/images/hero.png" />
    <a data-link="cta_link" href="/signup">Sign Up</a>
    <section data-repeatable="features">
        <div>
            <h3 data-text="title">Feature A</h3>
            <p data-textarea="summary">Short summary</p>
        </div>
    </section>
</div>
HTML;

    $result = $parser->generate($html);

    expect($result)->toBeArray();
    expect($result['fields_config'])->toHaveCount(4);
    expect($result['mapping_config'])->toHaveCount(1);
    expect($result['defaults']['data'])->toMatchArray([
        'title' => 'My title',
        'description' => 'This is a description.',
        'hero_image' => '/images/hero.png',
        'cta_link' => [
            'text' => 'Sign Up',
            'url' => '/signup',
        ],
    ]);

    expect($result['fields_config'][0])->toMatchArray([
        'name' => 'title',
        'type' => 'text',
        'label' => 'Title',
    ]);
    expect($result['fields_config'][1])->toMatchArray([
        'name' => 'description',
        'type' => 'textarea',
        'label' => 'Description',
    ]);
    expect($result['fields_config'][2])->toMatchArray([
        'name' => 'hero_image',
        'type' => 'image',
        'label' => 'Hero Image',
    ]);
    expect($result['fields_config'][3])->toMatchArray([
        'name' => 'cta_link',
        'type' => 'link',
        'label' => 'Cta Link',
    ]);

    expect($result['mapping_config'][0]['group_name'])->toBe('features');
    expect($result['mapping_config'][0]['parent_group'])->toBeNull();
    expect($result['mapping_config'][0]['fields'][0])->toMatchArray([
        'name' => 'title',
        'type' => 'text',
        'label' => 'Title',
        'required' => false,
        'placeholder' => '',
        'default' => 'Feature A',
    ]);
    expect($result['mapping_config'][0]['fields'][1])->toMatchArray([
        'name' => 'summary',
        'type' => 'textarea',
        'label' => 'Summary',
        'required' => false,
        'placeholder' => '',
        'default' => 'Short summary',
    ]);

    expect($result['defaults']['mapping_items'])->toHaveKey('features');
    expect($result['defaults']['mapping_items']['features'][0])->toMatchArray([
        'title' => 'Feature A',
        'summary' => 'Short summary',
    ]);
});

it('supports repeatable fields from a top-level element and outputs item placeholders', function () {
    $parser = new CmsHtmlParser();

    $html = <<<HTML
<p data-repeatable="para" data-text="content">Lorem ipsum dolor sit amet.</p>
HTML;

    $result = $parser->generate($html);

    expect($result['mapping_config'][0]['group_name'])->toBe('para');
    expect($result['mapping_config'][0]['parent_group'])->toBeNull();
    expect($result['mapping_config'][0]['fields'][0])->toMatchArray([
        'name' => 'content',
        'type' => 'text',
        'label' => 'Content',
        'required' => false,
        'placeholder' => '',
        'default' => 'Lorem ipsum dolor sit amet.',
    ]);

    expect($result['template'])->toContain('{item.content}');
    expect($result['defaults']['mapping_items']['para'][0])->toMatchArray([
        'content' => 'Lorem ipsum dolor sit amet.',
    ]);
});

it('parses scalar fields from element text and outputs text placeholders', function () {
    $parser = new CmsHtmlParser();

    $html = <<<HTML
<div>
    <span data-number="price">100</span>
    <span data-email="contact_email">hello@example.com</span>
    <span data-date="publish_date">September 3, 2026</span>
    <span data-url="website">https://example.com</span>
</div>
HTML;

    $result = $parser->generate($html);

    expect($result['defaults']['data'])->toMatchArray([
        'price' => '100',
        'contact_email' => 'hello@example.com',
        'publish_date' => 'September 3, 2026',
        'website' => 'https://example.com',
    ]);
    expect($result['template'])->toContain('<span>{price}</span>');
    expect($result['template'])->toContain('<span>{contact_email}</span>');
    expect($result['template'])->toContain('<span>{publish_date}</span>');
    expect($result['template'])->toContain('<span>{website}</span>');
    expect($result['template'])->not->toContain('value="{');
});

it('keeps link text and url grouped under one logical field', function () {
    $result = (new CmsHtmlParser())->generate(
        '<a data-link="contact" href="/contact">Contact Us</a>'
    );

    expect($result['fields_config'])->toHaveCount(1);
    expect($result['fields_config'][0])->toMatchArray([
        'name' => 'contact',
        'type' => 'link',
        'default' => [
            'text' => 'Contact Us',
            'url' => '/contact',
        ],
    ]);
    expect($result['defaults']['data']['contact'])->toBe([
        'text' => 'Contact Us',
        'url' => '/contact',
    ]);
    expect($result['template'])->toContain('href="{contact.url}"');
    expect($result['template'])->toContain('{contact.text}');
});

it('creates text fields and defaults for consecutive plain repeatable list items', function () {
    $html = <<<'HTML'
<ol>
    <li data-repeatable="list">Engineering</li>
    <li>Management</li>
    <li>Computer Applications</li>
    <li>Pharmacy</li>
</ol>
HTML;

    $result = (new CmsHtmlParser())->generate($html);

    expect($result['mapping_config'][0]['group_name'])->toBe('list');
    expect($result['mapping_config'][0]['fields'][0])->toMatchArray([
        'name' => 'item',
        'type' => 'text',
        'default' => 'Engineering',
    ]);
    expect($result['defaults']['mapping_items']['list'])->toBe([
        ['item' => 'Engineering'],
        ['item' => 'Management'],
        ['item' => 'Computer Applications'],
        ['item' => 'Pharmacy'],
    ]);
    expect(substr_count($result['template'], '{item.item}'))->toBe(1);
});
