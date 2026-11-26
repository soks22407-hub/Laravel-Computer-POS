@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Add Product</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route ('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{route ('admin.products')}}">
                        <div class="text-tiny">Products</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Add product</div>
                </li>
            </ul>
        </div>
        <!-- form-add-product -->
        <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data"
            action="{{route ('admin.product.store')}}">
            @csrf
            <div class="wg-box">
                <fieldset class="name">
                    <div class="body-title mb-10">Product Code</div>
                    <input class="mb-10" type="text" placeholder="Enter product code"
                        name="code" tabindex="0" value="{{old('code')}}">
                </fieldset>
                @error('code') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="name">
                    <div class="body-title mb-10">Product name <span class="tf-color-1">*</span>
                    </div>
                    <input class="mb-10" type="text" placeholder="Enter product name"
                        name="name" tabindex="0" value="{{old('name')}}" aria-required="true" required="">
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('name') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="name">
                    <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product slug"
                        name="slug" tabindex="0" value="{{old('slug')}}" aria-required="true" required="">
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('slug') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <div class="gap22 cols">
                    <fieldset class="category">
                        <div class="body-title mb-10">Category <span class="tf-color-1">*</span>
                        </div>
                        <div class="select">
                            <select class="" name="category_id">
                                <option>Choose category</option>
                                @foreach ($categories as $Categories)
                                <option value="{{ $Categories->id}}">{{ $Categories->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="brand">
                        <div class="body-title mb-10">Brand <span class="tf-color-1">*</span>
                        </div>
                        <div class="select">
                            <select class="" name="brand_id">
                                <option>Choose Brand</option>
                                @foreach ($brands as $brand)
                                <option value="{{ $brand->id}}">{{ $brand->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                </div>

                <fieldset class="shortdescription">
                    <div class="body-title mb-10">Short Description <span
                            class="tf-color-1">*</span></div>
                    <textarea class="mb-10 ht-150" name="short_description"
                        placeholder="Short Description" tabindex="0" aria-required="true"
                        required="">{{old('short_description')}}</textarea>
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('short_description') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="description">
                    <div class="body-title mb-10">Description <span class="tf-color-1">*</span>
                    </div>
                    <textarea class="mb-10" name="description" placeholder="Description"
                        tabindex="0" aria-required="true" required="">{{old('description')}}</textarea>
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('description') <span class="alert alert-danger text-center">{{$message}}</span> @enderror

                <div class="wg-box mt-4">
                    <h4 class="mb-20">Specifications</h4>

                    @php
                    $specSections = [
                    <!-- 'network' => ['Technology', '2G bands', '3G bands', '4G bands', '5G bands', 'Speed'], -->
                    'launch' => ['Announced', 'Status'],
                    'body' => ['Dimensions', 'Weight', 'Build', 'SIM'],
                    'display' => ['Type', 'Size', 'Resolution', 'Protection'],
                    'platform'=> ['OS', 'Chipset', 'CPU', 'GPU'],
                    'memory' => ['Card slot', 'Internal'],
                    'main_camera' => ['Triple', 'Single', 'Features', 'Video'],
                    'selfie_camera' => ['Single', 'Features', 'Video'],
                    'sound' => ['Loudspeaker', '3.5mm jack'],
                    'comms' => ['WLAN', 'Bluetooth', 'Positioning', 'NFC', 'Infrared port', 'Radio', 'USB'],
                    'features'=> ['Sensors'],
                    'battery' => ['Type', 'Charging'],
                    'misc' => ['Colors', 'Models'],
                    ];


                    $existingSpecs = [];
                    if (isset($product)) {
                    foreach ($product->attributes as $attr) {
                    $parts = explode('.', $attr->name, 2);
                    if (count($parts) === 2) {
                    $existingSpecs[$parts[0]][$parts[1]] = $attr->value;
                    }
                    }
                    }
                    @endphp

                    @foreach($specSections as $group => $fields)
                    <div class="mb-20">

                        {{-- Section Header --}}
                        <div style="background:#ECF3FF; padding:8px 14px;
                    border-radius:8px; margin-bottom:12px;">
                            <span style="color:#2275fc; font-weight:700;
                         font-size:13px; text-transform:uppercase;
                         letter-spacing:1px;">
                                {{ strtoupper(str_replace('_', ' ', $group)) }}
                            </span>
                        </div>

                        {{-- Two column grid --}}
                        <div class="cols gap22">
                            @foreach($fields as $field)
                            <fieldset>
                                <label class="body-title mb-5">{{ $field }}</label>
                                <input
                                    type="text"
                                    name="specs[{{ $group }}][{{ $field }}]"
                                    placeholder="Enter {{ $field }}"
                                    value="{{ $existingSpecs[$group][$field] ?? old('specs.' . $group . '.' . $field) }}">
                            </fieldset>
                            @endforeach
                        </div>

                        <div class="divider mt-15 mb-15"></div>
                    </div>
                    @endforeach

                </div>
            </div>

            <div class="wg-box">
                <fieldset>
                    <div class="body-title">Upload images <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="display:none">
                            <img src="../../../localhost_8000/images/upload/upload-1.png"
                                class="effect8" alt="">
                        </div>
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                <input type="file" id="myFile" name="image" accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>



                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">Regular Price <span
                                class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter regular price"
                            name="regular_price" tabindex="0" value="{{old('regular_price')}}" aria-required="true"
                            required="">
                    </fieldset>
                    @error('regular_price') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title mb-10">Sale Price <span
                                class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter sale price"
                            name="sale_price" tabindex="0" value="{{old('sale_price')}}">
                    </fieldset>
                    @error('sale_price') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title mb-10">Cost<span
                                class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter cost price"
                            name="cost_price" tabindex="0" value="{{old('cost_price')}}" aria-required="true"
                            required="">
                    </fieldset>
                    @error('cost_price') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                </div>


                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">SKU <span class="tf-color-1">*</span>
                        </div>
                        <input class="mb-10" type="text" placeholder="Enter SKU" name="SKU"
                            tabindex="0" value="{{old('SKU')}}" aria-required="true" required="">
                    </fieldset>
                    @error('SKU') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title mb-10">Quantity <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter quantity"
                            name="quantity" id="total-quantity" tabindex="0"
                            value="{{old('quantity', $product->quantity ?? '')}}"
                            aria-required="true" required=""
                            readonly style="background:#f5f5f5; cursor:not-allowed;">
                    </fieldset>
                    @error('quantity') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                </div>

                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">Stock</div>
                        <div class="select mb-10">
                            <select class="" name="stock_status">
                                <option value="instock">InStock</option>
                                <option value="outofstock">Out of Stock</option>
                            </select>
                        </div>
                    </fieldset>
                    @error('stock_status') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title mb-10">Barcode</div>
                        <input class="mb-10" type="text" placeholder="Enter barcode number (e.g. 1234567890)"
                            name="barcode" id="barcode-input" value="{{old('barcode', $product->barcode ?? '')}}">
                    </fieldset>
                    @error('barcode') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    {{-- Live barcode preview --}}
                    <div id="barcode-preview-wrap" style="display:none; margin-top:10px;">
                        <div class="body-title mb-5">Barcode Preview</div>
                        <div id="barcode-preview" style="background:#fff; padding:12px; border:1px solid #ddd; border-radius:8px; display:inline-block;">
                            <svg id="barcode-svg"></svg>
                        </div>
                    </div>
                </div>

                <div class="wg-box mt-4">
                    <h4 class="mb-3">Product Variants</h4>
                    <!-- <div id="variants-wrapper">
                        <div class="variant-item cols gap22 mb-2"> -->
                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Color</div>
                            <input type="text" name="variants[0][color]" placeholder="e.g. Black">
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Storage</div>
                            <input type="text" name="variants[0][storage]" placeholder="e.g. 512GB">
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Ram</div>
                            <input type="text" name="variants[0][ram]" placeholder="e.g. 16GB">
                        </fieldset>
                    </div>

                    <div class="cols gap22 mt-20">
                        <fieldset class="name">
                            <div class="body-title mb-10">Price</div>
                            <input type="number" name="variants[0][price]" placeholder="0.00">
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Quantity</div>
                            <input type="number" name="variants[0][quantity]" placeholder="e.g. 10">
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Barcode</div>
                            <input type="text"
                                name="variants[0][barcode]"
                                class="variant-barcode-input"
                                value="{{ $variant->barcode ?? '' }}"
                                placeholder="e.g. 1234567890">

                            <div class="barcode-preview-wrap mt-2" style="display:none;">
                                <svg class="variant-barcode-svg"></svg>
                            </div>
                        </fieldset>
                    </div>
                    <fieldset>
                        <div class="body-title">Upload images <span class="tf-color-1">*</span>
                        </div>
                        <div class="upload-image flex-grow">
                            <div class="item" id="imgpreview" style="display:none">
                                <img src="../../../localhost_8000/images/upload/upload-1.png"
                                    class="effect8" alt="">
                            </div>
                            <div class="item up-load">
                                <label class="uploadfile">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>
                                    <span class="body-text">Drop your images here or select <span
                                            class="tf-color">click to browse</span></span>
                                    <input type="file" class="variant-main-image" name="variants[0][image]" accept="image/*">
                                </label>
                                <div class="preview-main mt-2"></div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="body-title mb-10">Upload Variant Images </div>
                        <div class="upload-image mb-16">
                            <div class="item up-load">
                                <label class="uploadfile">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>
                                    <span class="text-tiny">Drop your images here or select <span
                                            class="tf-color">click to browse</span></span>
                                    <input type="file" class="variant-image" name="variants[0][images][]" multiple>
                                </label>
                                <div class="preview-box d-flex flex-wrap gap-2 mt-2"></div>
                            </div>
                        </div>
                    </fieldset>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" id="add-variant" class="tf-button mt-2">
                            <i class="icon-plus"></i> Add Variant
                        </button>
                    </div>

                    <div id="variants-container"></div>

                </div>
                <div class="cols gap10">
                    <button class="tf-button w-full" type="submit">Add product</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    $(function() {
        $("#myFile").on("change", function(e) {
            const photoInp = $("#myFile");
            const [file] = this.files;
            if (file) {
                $("#imgpreview img").attr('src', URL.createObjectURL(file));
                $("#imgpreview").show();
            }
        });

        $("#gFile").on("change", function(e) {
            const photoInp = $("#gFile");
            const gphotos = this.files;
            $.each(gphotos, function(key, val) {
                $("#galUpload").prepend(`<div class = "item gitems"><img src = "${URL.createObjectURL(val)}" /></div> `);
            });
        });

        $("input[name='name']").on("change", function() {
            $("input[name='slug']").val(StringToSlug($(this).val()));
        });

        //  Auto sum variant quantities → total quantity
        function recalculateTotalQuantity() {
            let total = 0;
            $("input[name*='[quantity]']").each(function() {
                const val = parseInt($(this).val()) || 0;
                total += val;
            });
            $("#total-quantity").val(total);
        }

        // Run on page load
        recalculateTotalQuantity();

        // Run whenever any variant quantity changes
        $(document).on("input change", "input[name*='[quantity]']", function() {
            recalculateTotalQuantity();
        });

        $("#add-variant").on("click", function() {
            // Count current variants for unique naming: variants[0], variants[1], etc.
            const index = $(".variant-item").length + 1;

            const html = `
            <div class="variant-item wg-box mt-3" style="border: 1px solid #eee; padding: 15px;">
                <div class="flex items-center justify-between mb-3">
                    <h5>Variant #${index + 1}</h5>
                    <button type="button" class="remove-variant-btn" style="color: red; border: none; background: none; cursor: pointer;">
                        <i class="icon-trash-2"></i> Remove
                    </button>
                </div>
                <div class="cols gap22">
                    <fieldset>
                        <label class="body-title mb-10">Color</label>
                        <input type="text" name="variants[${index}][color]" placeholder="e.g. Black">
                    </fieldset>
                    <fieldset>
                        <label class="body-title mb-10">Storage</label>
                        <input type="text" name="variants[${index}][storage]" placeholder="e.g. 512GB">
                    </fieldset>
                    <fieldset>
                        <label class="body-title mb-10">Ram</label>
                        <input type="text" name="variants[${index}][ram]" placeholder="e.g. 16GB">
                    </fieldset>
                </div>
                <div class="cols gap22 mt-3">
                    <fieldset>
                        <label class="body-title mb-10">Price</label>
                        <input type="number" name="variants[${index}][price]" placeholder="0.00">
                    </fieldset>
                    <fieldset>
                        <label class="body-title mb-10">Quantity</label>
                        <input type="number" name="variants[${index}][quantity]" placeholder="e.g. 10">
                    </fieldset>
                    <fieldset class="name">
                        <div class="body-title mb-10">Barcode</div>
                        <input type="text"
                            name="variants[${index}][barcode]"
                            class="variant-barcode-input"
                            value="{{ $variant->barcode ?? '' }}"
                            placeholder="e.g. 1234567890">

                        <div class="barcode-preview-wrap mt-2" style="display:none;">
                            <svg class="variant-barcode-svg"></svg>
                        </div>
                    </fieldset>
                </div>
                <fieldset>
                    <div class="body-title">Upload images <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="display:none">
                            <img src="../../../localhost_8000/images/upload/upload-1.png"
                                class="effect8" alt="">
                        </div>
                        <div class="item up-load">
                            <label class="uploadfile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                <input type="file" class="variant-main-image" name="variants[${index}][image]" accept="image/*">
                            </label>
                            <div class="preview-main mt-2"></div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <div class="body-title mb-10">Upload Variant Images </div>
                    <div class="upload-image mb-16">
                        <div class="item up-load">
                            <label class="uploadfile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="text-tiny">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                <input type="file" class="variant-image" name="variants[${index}][images][]" multiple>
                            </label>
                            <div class="preview-box d-flex flex-wrap gap-2 mt-2"></div>
                        </div>
                    </div>
                </fieldset>
            </div>
        `;

            $("#variants-container").append(html);
        });


        $(document).on("change", ".variant-main-image", function() {

            const file = this.files[0];
            const previewBox = $(this).closest(".upload-image").find(".preview-main");

            previewBox.html("");

            if (file) {
                const url = URL.createObjectURL(file);

                previewBox.html(`
            <img src="${url}" width="120" style="border-radius:8px;">
        `);
            }
        });


        $(document).on("change", ".variant-image", function() {
            const files = this.files;
            const previewBox = $(this).closest(".upload-image").find(".preview-box");

            previewBox.html("");

            $.each(files, function(_, file) {
                previewBox.append(`
                <div class="item gitems">
                    <img src="${URL.createObjectURL(file)}">
                </div>
                `);
            });
        });

        $(document).on("click", ".remove-variant-btn", function() {
            $(this).closest(".variant-item").remove();
        });

        document.getElementById('barcode-input').addEventListener('input', function() {
            const val = this.value.trim();
            const wrap = document.getElementById('barcode-preview-wrap');
            if (val.length >= 4) {
                try {
                    JsBarcode("#barcode-svg", val, {
                        format: "CODE128",
                        width: 2,
                        height: 60,
                        displayValue: true,
                        fontSize: 14,
                    });
                    wrap.style.display = 'block';
                } catch (e) {
                    wrap.style.display = 'none';
                }
            } else {
                wrap.style.display = 'none';
            }
        });

        // Show preview on page load if editing existing product
        window.addEventListener('load', function() {
            const input = document.getElementById('barcode-input');
            if (input && input.value.trim().length >= 4) {
                input.dispatchEvent(new Event('input'));
            }
        });

        $(document).on("input", ".variant-barcode-input", function() {
            const val = $(this).val().trim();
            const wrap = $(this).siblings('.barcode-preview-wrap');
            const svg = wrap.find('.variant-barcode-svg')[0];

            if (val.length >= 4) {
                try {
                    JsBarcode(svg, val, {
                        format: "CODE128",
                        width: 2,
                        height: 50,
                        displayValue: true,
                        fontSize: 12,
                    });
                    wrap.show();
                } catch (e) {
                    wrap.hide();
                }
            } else {
                wrap.hide();
            }
        });


    });



    function StringToSlug(Text) {
        return Text.toLowerCase()
            .replace(/[^\w ]+/g, "")
            .replace(/ +/g, "-");
    }
</script>
@endpush