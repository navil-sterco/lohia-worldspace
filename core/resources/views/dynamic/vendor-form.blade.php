@include('includes.header')

<section class="vendor_sec">
    <div class="container-lg">
        <div class="sec_title">
            <h1 class="title21">Become a Lohia Worldspace Vendor</h1>
        </div>

     

        <div class="vendor_grid">
            <div class="cnt_victor">
                <img src="{{ asset('frontend-assets/images/victor-dash10.svg') }}" alt="victor" class="img-fluid w-100">
            </div>
            <div class="vendor_form">
                <h3 class="title48">FILL UP TO A LOHIA WORLDSPACE VENDOR</h3>

                <form action="{{ route('contact.store') }}" method="POST" id="vendorForm">
                    @csrf
                    <input type="hidden" name="interest" value="vendor">
                    <input type="hidden" name="name" id="hidden_name">
                    <input type="hidden" name="email" id="hidden_email">
                    <input type="hidden" name="phone" id="hidden_phone">

                    <div class="vendorform_wrap">
                        <div class="vendorform_column">
                            <h5 class="title21">Company Details</h5>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_company_name"
                                            value="{{ old('vendor_company_name') }}"
                                            class="form-control @error('vendor_company_name') is-invalid @enderror"
                                            placeholder="Name of the Company">
                                        @error('vendor_company_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_address" value="{{ old('vendor_address') }}"
                                            class="form-control @error('vendor_address') is-invalid @enderror"
                                            placeholder="Address of Registered Office">
                                        @error('vendor_address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vendorform_column">
                            <h5 class="title21">Contact Person, Telephone, Fax, Email</h5>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_contact_person"
                                            value="{{ old('vendor_contact_person') }}"
                                            class="form-control @error('vendor_contact_person') is-invalid @enderror"
                                            placeholder="Contact Person">
                                        @error('vendor_contact_person')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <select name="vendor_phone_code" class="form-select">
                                                <option value="+91">INDIA +91</option>
                                                <option value="+376">ANDORRA +376</option>
                                                <option value="+971">UAE +971</option>
                                                <option value="+93">AFGHANISTAN +93</option>
                                                <option value="+1268">ANTIGUA AND BARBUDA +1268</option>
                                                <option value=" +1264">ANGUILLA +1264</option>
                                                <option value="+355">ALBANIA +355</option>
                                                <option value=" +374">ARMENIA +374</option>
                                                <option value=" +599">NETHERLANDS +599</option>
                                                <option value=" +244">ANGOLA +244</option>
                                                <option value=" +672">ANTARCTICA +672</option>
                                                <option value=" +54">ARGENTINA +54</option>
                                                <option value=" +43">AUSTRIA +43</option>
                                                <option value=" +61">AUSTRALIA +61</option>
                                                <option value=" +297">ARUBA +297</option>
                                                <option value=" +994">AZERBAIJAN +994</option>
                                                <option value=" +387">BOSNIA +387</option>
                                                <option value=" +1246">BARBADOS +1246</option>
                                                <option value=" +880">BANGLADESH +880</option>
                                                <option value=" +32">BELGIUM +32</option>
                                                <option value="  +226">BURKINA FASO +226</option>
                                                <option value=" +359">BULGARIA +359</option>
                                                <option value=" +973">BAHRAIN +973</option>
                                                <option value=" +257">BURUNDI +257</option>
                                                <option value=" +229">BENIN +229</option>
                                                <option value="  +590">SAINT BARTHELEMY +590</option>
                                                <option value=" +1441">BERMUDA +1441</option>
                                                <option value="  +673">BRUNEI DARUSSALAM +673</option>
                                                <option value=" +591">BOLIVIA +591</option>
                                                <option value=" +55">BRAZIL +55</option>
                                                <option value=" +1242">BAHAMAS +1242</option>
                                                <option value=" +975">BHUTAN +975</option>
                                                <option value=" +267">BOTSWANA +267</option>
                                                <option value=" +375">BELARUS +375</option>
                                                <option value=" +501">BELIZE +501</option>
                                                <option value=" +1">CANADA +1</option>
                                                <option value="  +61">COCOS ISLANDS +61</option>
                                                <option value="  +243">CONGO REPUBLIC +243</option>
                                                <option value="  +236">CENTRAL AFRICA +236</option>
                                                <option value=" +242">CONGO +242</option>
                                                <option value=" +41">SWITZERLAND +41</option>
                                                <option value="+225">COTE D IVOIRE +225</option>
                                                <option value="  +682">COOK ISLANDS +682</option>
                                                <option value=" +56">CHILE +56</option>
                                                <option value="CAMEROON +237">CAMEROON +237</option>
                                                <option value=" +86">CHINA +86</option>
                                                <option value=" +57">COLOMBIA +57</option>
                                                <option value="  +506">COSTA RICA +506</option>
                                                <option value=" +53">CUBA +53</option>
                                                <option value="  +238">CAPE VERDE +238</option>
                                                <option value="  +61">CHRISTMAS ISLAND +61</option>
                                                <option value=" +357">CYPRUS +357</option>
                                                <option value="  +420">CZECH REPUBLIC +420</option>
                                                <option value=" +49">GERMANY +49</option>
                                                <option value=" +253">DJIBOUTI +253</option>
                                                <option value=" +45">DENMARK +45</option>
                                                <option value=" +1767">DOMINICA +1767</option>
                                                <option value="  +1809">DOMINICAN REPUBLIC +1809</option>
                                                <option value=" +213">ALGERIA +213</option>
                                                <option value=" +593">ECUADOR +593</option>
                                                <option value=" +372">ESTONIA +372</option>
                                                <option value=" +20">EGYPT +20</option>
                                                <option value=" +291">ERITREA +291</option>
                                                <option value=" +34">SPAIN +34</option>
                                                <option value=" +251">ETHIOPIA +251</option>
                                                <option value=" +358">FINLAND +358</option>
                                                <option value=" +679">FIJI +679</option>
                                                <option value="  +500">FALKLAND ISLANDS +500</option>
                                                <option value=" +691">MICRONESIA +691</option>
                                                <option value="  +298">FAROE ISLANDS +298</option>
                                                <option value=" +33">FRANCE +33</option>
                                                <option value=" +241">GABON +241</option>
                                                <option value=" +44">UK +44</option>
                                                <option value=" +1473">GRENADA +1473</option>
                                                <option value=" +995">GEORGIA +995</option>
                                                <option value=" +233">GHANA +233</option>
                                                <option value=" +350">GIBRALTAR +350</option>
                                                <option value=" +299">GREENLAND +299</option>
                                                <option value=" +220">GAMBIA +220</option>
                                                <option value=" +224">GUINEA +224</option>
                                                <option value="  +240">EQUATORIAL GUINEA +240</option>
                                                <option value=" +30">GREECE +30</option>
                                                <option value=" +502">GUATEMALA +502</option>
                                                <option value="GUAM +1671">GUAM +1671</option>
                                                <option value=" +245">GUINEA-BISSAU +245</option>
                                                <option value=" +592">GUYANA +592</option>
                                                <option value="  +852">HONG KONG +852</option>
                                                <option value=" +504">HONDURAS +504</option>
                                                <option value=" +385">CROATIA +385</option>
                                                <option value=" +509">HAITI +509</option>
                                                <option value=" +36">HUNGARY +36</option>
                                                <option value=" +62">INDONESIA +62</option>
                                                <option value=" +353">IRELAND +353</option>
                                                <option value=" +972">ISRAEL +972</option>
                                                <option value=" +44">ISLE OF MAN +44</option>
                                                <option value="INDIA +91">INDIA +91</option>
                                                <option value=" +964">IRAQ +964</option>
                                                <option value=" +98">IRAN +98</option>
                                                <option value=" +354">ICELAND +354</option>
                                                <option value=" +39">ITALY +39</option>
                                                <option value=" +1876">JAMAICA +1876</option>
                                                <option value=" +962">JORDAN +962</option>
                                                <option value=" +81">JAPAN +81</option>
                                                <option value=" +254">KENYA +254</option>
                                                <option value=" +996">KYRGYZSTAN +996</option>
                                                <option value=" +855">CAMBODIA +855</option>
                                                <option value=" +686">KIRIBATI +686</option>
                                                <option value=" +269">COMOROS +269</option>
                                                <option value="+1869">SAINT KITTS AND NEVIS +1869</option>
                                                <option value="+850">KOREA +850</option>
                                                <option value=" +82">KOREA REPUBLIC OF +82</option>
                                                <option value=" +965">KUWAIT +965</option>
                                                <option value="  +1345">CAYMAN ISLANDS +1345</option>
                                                <option value=" +7">KAZAKSTAN +7</option>
                                                <option value=" +961">LEBANON +961</option>
                                                <option value="  +1758">SAINT LUCIA +1758</option>
                                                <option value=" +423">LIECHTENSTEIN +423</option>
                                                <option value="  +94">SRI LANKA +94</option>
                                                <option value=" +231">LIBERIA +231</option>
                                                <option value=" +266">LESOTHO +266</option>
                                                <option value=" +370">LITHUANIA +370</option>
                                                <option value=" +352">LUXEMBOURG +352</option>
                                                <option value=" +371">LATVIA +371</option>
                                                <option value=" +218">LIBYAN +218</option>
                                                <option value=" +212">MOROCCO +212</option>
                                                <option value=" +377">MONACO +377</option>
                                                <option value=" +373">MOLDOVA +373</option>
                                                <option value=" +382">MONTENEGRO +382</option>
                                                <option value="  +1599">SAINT MARTIN +1599</option>
                                                <option value=" +261">MADAGASCAR +261</option>
                                                <option value=" +692">MARSHALL ISLANDS +692</option>
                                                <option value=" +389">MACEDONIA +389</option>
                                                <option value=" +223">MALI +223</option>
                                                <option value=" +95">MYANMAR +95</option>
                                                <option value=" +976">MONGOLIA +976</option>
                                                <option value=" +853">MACAU +853</option>
                                                <option value="   +1670">NORTHERN MARIANA ISLANDS +1670</option>
                                                <option value=" +222">MAURITANIA +222</option>
                                                <option value=" +1664">MONTSERRAT +1664</option>
                                                <option value=" +356">MALTA +356</option>
                                                <option value=" +230">MAURITIUS +230</option>
                                                <option value=" +960">MALDIVES +960</option>
                                                <option value=" +265">MALAWI +265</option>
                                                <option value=" +52">MEXICO +52</option>
                                                <option value=" +60">MALAYSIA +60</option>
                                                <option value=" +258">MOZAMBIQUE +258</option>
                                                <option value=" +264">NAMIBIA +264</option>
                                                <option value="  +687">NEW CALEDONIA +687</option>
                                                <option value=" +227">NIGER +227</option>
                                                <option value=" +234">NIGERIA +234</option>
                                                <option value=" +505">NICARAGUA +505</option>
                                                <option value=" +31">NETHERLANDS +31</option>
                                                <option value=" +47">NORWAY +47</option>
                                                <option value=" +977">NEPAL +977</option>
                                                <option value=" +674">NAURU +674</option>
                                                <option value=" +683">NIUE +683</option>
                                                <option value="  +64">NEW ZEALAND +64</option>
                                                <option value=" +968">OMAN +968</option>
                                                <option value=" +507">PANAMA +507</option>
                                                <option value=" +51">PERU +51</option>
                                                <option value=" +689">POLYNESIA +689</option>
                                                <option value="   +675">PAPUA NEW GUINEA +675</option>
                                                <option value=" +63">PHILIPPINES +63</option>
                                                <option value=" +92">PAKISTAN +92</option>
                                                <option value=" +48">POLAND +48</option>
                                                <option value="    +508">SAINT PIERRE AND MIQUELON +508</option>
                                                <option value=" +870">PITCAIRN +870</option>
                                                <option value="  +1">PUERTO RICO +1</option>
                                                <option value=" +351">PORTUGAL +351</option>
                                                <option value=" +680">PALAU +680</option>
                                                <option value=" +595">PARAGUAY +595</option>
                                                <option value=" +974">QATAR +974</option>
                                                <option value=" +40">ROMANIA +40</option>
                                                <option value=" +381">SERBIA +381</option>
                                                <option value="  +7">RUSSIAN FEDERATION +7</option>
                                                <option value=" +250">RWANDA +250</option>
                                                <option value="  +966">SAUDI ARABIA +966</option>
                                                <option value="  +677">SOLOMON ISLANDS +677</option>
                                                <option value=" +248">SEYCHELLES +248</option>
                                                <option value=" +249">SUDAN +249</option>
                                                <option value=" +46">SWEDEN +46</option>
                                                <option value=" +65">SINGAPORE +65</option>
                                                <option value="  +290">SAINT HELENA +290</option>
                                                <option value=" +386">SLOVENIA +386</option>
                                                <option value=" +421">SLOVAKIA +421</option>
                                                <option value="  +232">SIERRA LEONE +232</option>
                                                <option value="  +378">SAN MARINO +378</option>
                                                <option value=" +221">SENEGAL +221</option>
                                                <option value=" +252">SOMALIA +252</option>
                                                <option value=" +597">SURINAME +597</option>
                                                <option value="  +503">EL SALVADOR +503</option>
                                                <option value=" +963">SYRIA +963</option>
                                                <option value=" +268">SWAZILAND +268</option>
                                                <option value="  +1649">TURKS ISLANDS +1649</option>
                                                <option value=" +235">CHAD +235</option>
                                                <option value="TOGO +228">TOGO +228</option>
                                                <option value=" +66">THAILAND +66</option>
                                                <option value=" +992">TAJIKISTAN +992</option>
                                                <option value=" +690">TOKELAU +690</option>
                                                <option value="+670">TIMOR-LESTE +670</option>
                                                <option value=" +993">TURKMENISTAN +993</option>
                                                <option value=" +216">TUNISIA +216</option>
                                                <option value=" +676">TONGA +676</option>
                                                <option value=" +90">TURKEY +90</option>
                                                <option value="   +1868">TRINIDAD AND TOBAGO +1868</option>
                                                <option value=" +688">TUVALU +688</option>
                                                <option value=" +886">TAIWAN +886</option>
                                                <option value=" +255">TANZANIA +255</option>
                                                <option value=" +380">UKRAINE +380</option>
                                                <option value=" +256">UGANDA +256</option>
                                                <option value="  +1">UNITED STATES +1</option>
                                                <option value=" +598">URUGUAY +598</option>
                                                <option value=" +998">UZBEKISTAN +998</option>
                                                <option value=" +39">HOLY SEE +39</option>
                                                <option value="  +1784">SAINT VINCENT +1784</option>
                                                <option value=" +58">VENEZUELA +58</option>
                                                <option value="  +1284">VIRGIN ISLANDS +1284</option>
                                                <option value="  +1340">VIRGIN ISLANDS +1340</option>
                                                <option value="  +84">VIET NAM +84</option>
                                                <option value=" +678">VANUATU +678</option>
                                                <option value="   +681">WALLIS AND FUTUNA +681</option>
                                                <option value=" +685">SAMOA +685</option>
                                                <option value=" +381">KOSOVO +381</option>
                                                <option value=" +967">YEMEN +967</option>
                                                <option value=" +262">MAYOTTE +262</option>
                                                <option value="  +27">SOUTH AFRICA +27</option>
                                                <option value=" +260">ZAMBIA +260</option>
                                                <option value=" +263">ZIMBABWE +263</option>
                                            </select>
                                            <input type="text" name="vendor_phone"
                                                value="{{ old('vendor_phone') }}"
                                                class="form-control @error('vendor_phone') is-invalid @enderror"
                                                placeholder="Phone Number*">
                                        </div>
                                        @error('vendor_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_fax" value="{{ old('vendor_fax') }}"
                                            class="form-control @error('vendor_fax') is-invalid @enderror"
                                            placeholder="FAX">
                                        @error('vendor_fax')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_email" value="{{ old('vendor_email') }}"
                                            class="form-control @error('vendor_email') is-invalid @enderror"
                                            placeholder="Email Address">
                                        @error('vendor_email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vendorform_column">
                            <h5 class="title21">Details of Ownership</h5>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_ownership_detail"
                                            value="{{ old('vendor_ownership_detail') }}"
                                            class="form-control @error('vendor_ownership_detail') is-invalid @enderror"
                                            placeholder="Details of Ownership">
                                        @error('vendor_ownership_detail')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <select name="vendor_category"
                                            class="form-select @error('vendor_category') is-invalid @enderror">
                                            <option value="">The category you belong to:</option>
                                            <option value="Manufacturer" @selected(old('vendor_category') === 'Manufacturer')>Manufacturer
                                            </option>
                                            <option value="Trader" @selected(old('vendor_category') === 'Trader')>Trader</option>
                                            <option value="Dealer" @selected(old('vendor_category') === 'Dealer')>Dealer</option>
                                        </select>
                                        @error('vendor_category')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vendorform_column">
                            <h5 class="title21">Registration Details (with various govt. authorities)</h5>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="vendor_registration_no"
                                            value="{{ old('vendor_registration_no') }}"
                                            class="form-control @error('vendor_registration_no') is-invalid @enderror"
                                            placeholder="Registration No.">
                                        @error('vendor_registration_no')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="vendor_registration_date"
                                            value="{{ old('vendor_registration_date') }}"
                                            class="form-control @error('vendor_registration_date') is-invalid @enderror"
                                            id="registrationDate" placeholder="Registration Date"
                                            onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                        @error('vendor_registration_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="vendor_gst" value="{{ old('vendor_gst') }}"
                                            class="form-control @error('vendor_gst') is-invalid @enderror"
                                            placeholder="GST No.">
                                        @error('vendor_gst')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="vendor_pan" value="{{ old('vendor_pan') }}"
                                            class="form-control @error('vendor_pan') is-invalid @enderror"
                                            placeholder="PAN No.">
                                        @error('vendor_pan')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="vendor_pf" value="{{ old('vendor_pf') }}"
                                            class="form-control @error('vendor_pf') is-invalid @enderror"
                                            placeholder="P.F No.">
                                        @error('vendor_pf')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="vendor_esi" value="{{ old('vendor_esi') }}"
                                            class="form-control @error('vendor_esi') is-invalid @enderror"
                                            placeholder="E.S.I Number">
                                        @error('vendor_esi')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_bank_name"
                                            value="{{ old('vendor_bank_name') }}"
                                            class="form-control @error('vendor_bank_name') is-invalid @enderror"
                                            placeholder="Bank Name">
                                        @error('vendor_bank_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_bank_branch"
                                            value="{{ old('vendor_bank_branch') }}"
                                            class="form-control @error('vendor_bank_branch') is-invalid @enderror"
                                            placeholder="Bank Branch">
                                        @error('vendor_bank_branch')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="vendor_bank_account"
                                            value="{{ old('vendor_bank_account') }}"
                                            class="form-control @error('vendor_bank_account') is-invalid @enderror"
                                            placeholder="Bank A/c No.">
                                        @error('vendor_bank_account')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="vendor_account_type"
                                            value="{{ old('vendor_account_type') }}"
                                            class="form-control @error('vendor_account_type') is-invalid @enderror"
                                            placeholder="Bank A/c Type">
                                        @error('vendor_account_type')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="vendor_ifsc" value="{{ old('vendor_ifsc') }}"
                                            class="form-control @error('vendor_ifsc') is-invalid @enderror"
                                            placeholder="IFSC No.">
                                        @error('vendor_ifsc')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vendorform_column">
                            <h5 class="title21">Are you a Member of any Trade Bodies / Associations?</h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <input type="text" name="vendor_trade_member"
                                            value="{{ old('vendor_trade_member') }}"
                                            class="form-control @error('vendor_trade_member') is-invalid @enderror"
                                            placeholder="Mention the names, Reg. No. and date">
                                        @error('vendor_trade_member')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vendorform_column">
                            <h5 class="title21">Summary of Services or Products and Capability</h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <input type="text" name="vendor_parent_companies"
                                            value="{{ old('vendor_parent_companies') }}"
                                            class="form-control @error('vendor_parent_companies') is-invalid @enderror"
                                            placeholder="Names of Parent, Associate and Subsidiary Companies">
                                        @error('vendor_parent_companies')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vendorform_column">
                            <h5 class="title21">Description of facilities (if applicable)</h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <input type="text" name="vendor_facility_area"
                                            value="{{ old('vendor_facility_area') }}"
                                            class="form-control @error('vendor_facility_area') is-invalid @enderror"
                                            placeholder="Technical/Manufacturing/Workshop Floor Areas (m2)">
                                        @error('vendor_facility_area')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vendorform_column">
                            <h5 class="title21">Details of Employees</h5>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_total_employees"
                                            value="{{ old('vendor_total_employees') }}"
                                            class="form-control @error('vendor_total_employees') is-invalid @enderror"
                                            placeholder="Total No of Employees">
                                        @error('vendor_total_employees')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_permanent_staff"
                                            value="{{ old('vendor_permanent_staff') }}"
                                            class="form-control @error('vendor_permanent_staff') is-invalid @enderror"
                                            placeholder="Number of Permanent Staff">
                                        @error('vendor_permanent_staff')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vendorform_column">
                            <h5 class="title21">Company Details</h5>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <select name="vendor_audited_accounts"
                                            class="form-select @error('vendor_audited_accounts') is-invalid @enderror">
                                            <option value="">Do you have audited accounts for the previous 2
                                                years.?</option>
                                            <option value="YES" @selected(old('vendor_audited_accounts') === 'YES')>Yes</option>
                                            <option value="NO" @selected(old('vendor_audited_accounts') === 'NO')>No</option>
                                        </select>
                                        @error('vendor_audited_accounts')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_service_backup"
                                            value="{{ old('vendor_service_backup') }}"
                                            class="form-control @error('vendor_service_backup') is-invalid @enderror"
                                            placeholder="Furnish details of post supply service back up provided by you">
                                        @error('vendor_service_backup')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_credit_period"
                                            value="{{ old('vendor_credit_period') }}"
                                            class="form-control @error('vendor_credit_period') is-invalid @enderror"
                                            placeholder="What is your maximum credit period that can be offered to us?">
                                        @error('vendor_credit_period')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vendorform_column">
                            <h5 class="title21">Company Experience</h5>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_years_experience"
                                            value="{{ old('vendor_years_experience') }}"
                                            class="form-control @error('vendor_years_experience') is-invalid @enderror"
                                            placeholder="How long have you been in business?">
                                        @error('vendor_years_experience')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_other_industries"
                                            value="{{ old('vendor_other_industries') }}"
                                            class="form-control @error('vendor_other_industries') is-invalid @enderror"
                                            placeholder="Which other Industries do you supply the product to?">
                                        @error('vendor_other_industries')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vendorform_column">
                            <h5 class="title21">Quality Management System Whether QMS as per ISO 9001: 2000 is
                                Implemented in your Organization?</h5>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_qms_since"
                                            value="{{ old('vendor_qms_since') }}"
                                            class="form-control @error('vendor_qms_since') is-invalid @enderror"
                                            placeholder="If yes, since when is it implemented?">
                                        @error('vendor_qms_since')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_certification_authority"
                                            value="{{ old('vendor_certification_authority') }}"
                                            class="form-control @error('vendor_certification_authority') is-invalid @enderror"
                                            placeholder="Mention the Certification authority and Certificate No.">
                                        @error('vendor_certification_authority')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_iso14001"
                                            value="{{ old('vendor_iso14001') }}"
                                            class="form-control @error('vendor_iso14001') is-invalid @enderror"
                                            placeholder="Do you follow EMS as per ISO 14001:2004?">
                                        @error('vendor_iso14001')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_ohsas18001"
                                            value="{{ old('vendor_ohsas18001') }}"
                                            class="form-control @error('vendor_ohsas18001') is-invalid @enderror"
                                            placeholder="Do you follow OHSAS as per ISO 18001:2007?">
                                        @error('vendor_ohsas18001')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_quality_tools"
                                            value="{{ old('vendor_quality_tools') }}"
                                            class="form-control @error('vendor_quality_tools') is-invalid @enderror"
                                            placeholder="Do you follow any Product/Process Quality system improvement tools like Lean, TQM etc.?">
                                        @error('vendor_quality_tools')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_other_info"
                                            value="{{ old('vendor_other_info') }}"
                                            class="form-control @error('vendor_other_info') is-invalid @enderror"
                                            placeholder="Any Other Information">
                                        @error('vendor_other_info')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vendorform_column">
                            <h5 class="title21">Name, Designation, Signature of the Person Completing this
                                Questionnaire</h5>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_completed_by"
                                            value="{{ old('vendor_completed_by') }}"
                                            class="form-control @error('vendor_completed_by') is-invalid @enderror"
                                            placeholder="Contact Person">
                                        @error('vendor_completed_by')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" name="vendor_completed_by_mobile"
                                            value="{{ old('vendor_completed_by_mobile') }}"
                                            class="form-control @error('vendor_completed_by_mobile') is-invalid @enderror"
                                            placeholder="Mobile No.">
                                        @error('vendor_completed_by_mobile')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                            @error('g-recaptcha-response')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="vendorform_submit">
                            <button type="submit" class="submit_btn">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('vendorForm');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Sync shared fields (name/email/phone) from vendor-specific inputs
            form.querySelector('[name="name"]').value = form.querySelector('[name="vendor_contact_person"]').value;
            form.querySelector('[name="email"]').value = form.querySelector('[name="vendor_email"]').value;

            // Concatenate phone code + phone number into vendor_phone itself
            var phoneCode = form.querySelector('[name="vendor_phone_code"]').value.trim();
            var phoneNumber = form.querySelector('[name="vendor_phone"]').value.trim();
            var fullPhone = phoneCode + ' ' + phoneNumber;

            form.querySelector('[name="vendor_phone"]').value = fullPhone;
            form.querySelector('[name="phone"]').value = fullPhone;

            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {
                        action: 'vendor_form'
                    })
                    .then(function(token) {
                        document.getElementById('g-recaptcha-response').value = token;
                        form.submit();
                    })
                    .catch(function() {
                        form.submit();
                    });
            });
        });
    });
</script>

@include('includes.footer')
