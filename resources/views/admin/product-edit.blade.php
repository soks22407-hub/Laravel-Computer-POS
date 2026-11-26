@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Product</h3>
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
                    <div class="text-tiny">Edit product</div>
                </li>
            </ul>
        </div>
        <!-- form-add-product -->
        <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data" action="{{route ('admin.product.update')}}">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{$product->id}}" />
            <div class="wg-box">
                <fieldset class="name">
                    <div class="body-title mb-10">Product Code</div>
                    <input class="mb-10" type="text" placeholder="Enter product code"
                        name="code" tabindex="0" value="{{$product->code}}">
                </fieldset>
                @error('code') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="name">
                    <div class="body-title mb-10">Product name <span class="tf-color-1">*</span>
                    </div>
                    <input class="mb-10" type="text" placeholder="Enter product name"
                        name="name" tabindex="0" value="{{$product->name}}" aria-required="true" required="">
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('name') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="name">
                    <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product slug"
                        name="slug" tabindex="0" value="{{$product->slug}}" aria-required="true" required="">
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
                                <option value="{{ $Categories->id}}" {{$product->category_id == $Categories->id ? "selected":""}}>{{ $Categories->name}}</option>
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
                                <option value="{{ $brand->id}}" {{$product->brand_id == $brand->id ? "selected":""}}>{{ $brand->name}}</option>
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
                        required="">{{$product->short_description}}</textarea>
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('short_description') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="description">
                    <div class="body-title mb-10">Description <span class="tf-color-1">*</span>
                    </div>
                    <textarea class="mb-10" name="description" placeholder="Description"
                        tabindex="0" aria-required="true" required="">{{$product->description}}</textarea>
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('description') <span class="alert alert-danger text-center">{{$message}}</span> @enderror

                <div class="wg-box mt-4">
                    <h4 class="mb-20">Specifications</h4>

                    @php
                    $specSections = [
                    'launch' => ['Announced', 'Status'],
                    'body' => ['Dimensions', 'Weight'],
                    'display' => ['Type', 'Size', 'Resolution'],
                    'platform'=> ['OS', 'CPU', 'GPU'],
                    'memory' => ['Card slot', 'Internal'],
                    'comms' => ['WLAN', 'USB'],
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

                        <div style="background:#ECF3FF; padding:8px 14px;
                    border-radius:8px; margin-bottom:12px;">
                            <span style="color:#2275fc; font-weight:700;
                         font-size:13px; text-transform:uppercase;
                         letter-spacing:1px;">
                                {{ strtoupper(str_replace('_', ' ', $group)) }}
                            </span>
                        </div>

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
                        @if($product->image)
                        <div class="item" id="imgpreview">
                            <img src="{{ asset('uploads/products/'.$product->image) }}?v={{ time() }}"
                                class="effect8" alt="{{$product->name}}">
                        </div>
                        @endif
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

                <!-- <fieldset>
                    <div class="body-title mb-10">Upload Gallery Images</div>
                    <div class="upload-image mb-16">
                        @if($product->images)
                        @foreach(explode(',', $product->images) as $img)
                        <div class="item gitems">
                            <img src="{{asset ('uploads/products')}}/{{trim($img)}}?v={{ time() }}" alt="">
                        </div>
                        @endforeach
                        @endif
                        <div id="galUpload" class="item up-load">
                            <label class="uploadfile" for="gFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="text-tiny">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                <input type="file" id="gFile" name="images[]" accept="image/*"
                                    multiple="">
                            </label>
                        </div>
                    </div>
                </fieldset> -->

                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">Regular Price <span
                                class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter regular price"
                            name="regular_price" tabindex="0" value="{{$product->regular_price}}" aria-required="true"
                            required="">
                    </fieldset>
                    @error('regular_price') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title mb-10">Sale Price <span
                                class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter sale price"
                            name="sale_price" tabindex="0" value="{{$product->sale_price}}">
                    </fieldset>
                    @error('sale_price') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title mb-10">Cost<span
                                class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter cost price"
                            name="cost_price" tabindex="0" value="{{$product->cost_price}}" aria-required="true"
                            required="">
                    </fieldset>
                    @error('cost_price') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                </div>


                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">SKU <span class="tf-color-1">*</span>
                        </div>
                        <input class="mb-10" type="text" placeholder="Enter SKU" name="SKU"
                            tabindex="0" value="{{$product->SKU}}" aria-required="true" required="">
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
                                <option value="instock" {{$product->stock_status == "instock" ? "selected":""}}>InStock</option>
                                <option value="outofstock" {{$product->stock_status == "outofstock" ? "selected":""}}>Out of Stock</option>
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

                    <div id="variants-container">
                        @foreach($product->variants as $index => $variant)
                        <div class="variant-item wg-box mt-3" style="border: 1px solid #eee; padding: 15px;">
                            <div class="flex items-center justify-between mb-3">
                                <h5>Variant #{{ $index + 1 }}</h5>
                                <button type="button" class="remove-variant-btn" style="color: red; border: none; background: none; cursor: pointer;">
                                    <i class="icon-trash-2"></i> Remove
                                </button>
                            </div>
                            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                            <input type="hidden" name="variants[{{ $index }}][old_image]" value="{{ $variant->image }}">

                            <div class="cols gap22">
                                <fieldset class="name">
                                    <div class="body-title mb-10">Color</div>
                                    <input type="text" name="variants[{{ $index }}][color]" value="{{ $variant->color }}" placeholder="e.g. Black">
                                </fieldset>
                                <fieldset class="name">
                                    <div class="body-title mb-10">Storage</div>
                                    <input type="text" name="variants[{{ $index }}][storage]" value="{{ $variant->storage }}" placeholder="e.g. 512GB">
                                </fieldset>
                                <fieldset class="name">
                                    <div class="body-title mb-10">Ram</div>
                                    <input type="text" name="variants[{{ $index }}][ram]" value="{{ $variant->ram }}" placeholder="e.g. 16GB">
                                </fieldset>
                            </div>

                            <div class="cols gap22 mt-20">
                                <fieldset class="name">
                                    <div class="body-title mb-10">Price</div>
                                    <input type="number" name="variants[{{ $index }}][price]" value="{{ $variant->price }}" placeholder="0.00">
                                </fieldset>
                                <fieldset class="name">
                                    <div class="body-title mb-10">Quantity</div>
                                    <input type="number" name="variants[{{ $index }}][quantity]" value="{{ $variant->quantity }}" placeholder="e.g. 10">
                                </fieldset>
                                <fieldset class="name">
                                    <div class="body-title mb-10">Barcode</div>
                                    <input type="text"
                                        name="variants[{{ $index }}][barcode]"
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
                                    @if($variant->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('uploads/variants/'.$variant->image) }}?v={{ time() }}" width="100">
                                    </div>
                                    @endif
                                    <div class="item up-load">
                                        <label class="uploadfile">
                                            <span class="icon">
                                                <i class="icon-upload-cloud"></i>
                                            </span>
                                            <span class="body-text">Drop your images here or select <span
                                                    class="tf-color">click to browse</span></span>
                                            <input type="file" class="variant-main-image" name="variants[{{ $index }}][image]" accept="image/*">
                                        </label>
                                        <div class="preview-main mt-2"></div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="body-title mb-10">Upload Variant Images </div>
                                <div class="upload-image mb-16">
                                    @if(!empty($variant->images))
                                    <div class="d-flex gap-2 flex-wrap mb-2">
                                        @foreach($variant->images as $img)
                                        <img src="{{ asset('uploads/variants/Thumbnails/'.$img) }}" width="80">
                                        @endforeach
                                    </div>
                                    @endif
                                    <div class="item up-load">
                                        <label class="uploadfile">
                                            <span class="icon">
                                                <i class="icon-upload-cloud"></i>
                                            </span>
                                            <span class="text-tiny">Drop your images here or select <span
                                                    class="tf-color">click to browse</span></span>
                                            <input type="file" class="variant-image" name="variants[{{ $index }}][images][]" multiple>
                                        </label>
                                        <div class="preview-box d-flex flex-wrap gap-2 mt-2"></div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" id="add-variant" class="tf-button mt-2">
                            <i class="icon-plus"></i> Add New Variant
                        </button>
                    </div>
                </div>
                <div class="cols gap10">
                    <button class="tf-button w-full" type="submit">Update product</button>
                </div>
            </div>
        </form>
        <!-- /form-add-product -->
    </div>
    <!-- /main-content-wrap -->
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


        $("#add-variant").on("click", function() {

            const index = $(".variant-item").length;

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
                            name="variants[{{ $index }}][barcode]"
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
                        @if(!empty($variant->images))
                            <div class="d-flex gap-2 flex-wrap mb-2">
                                @foreach($variant->images as $img)
                                    <img src="{{ asset('uploads/variants/Thumbnails/'.$img) }}" width="80">
                                @endforeach
                            </div>
                        @endif  
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


        function reindexVariants() {
            $(".variant-item").each(function(index) {
                $(this).find("input, select, textarea").each(function() {
                    let name = $(this).attr("name");
                    if (name) {
                        name = name.replace(/variants\[\d+\]/, `variants[${index}]`);
                        $(this).attr("name", name);
                    }
                });

                $(this).find("h5").text("Variant #" + (index + 1));
            });
        }

        $(document).on("click", ".remove-variant-btn", function() {
            $(this).closest(".variant-item").remove();
            reindexVariants();
            recalculateTotalQuantity();
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

        $(document).on("input", ".variant-barcode-input", function() {
            const val = $(this).val().trim();
            const wrap = $(this).siblings('.barcode-preview-wrap');
            const svg = wrap.find('.variant-barcode-svg')[0];

            if (!svg) return;

            if (val.length >= 4) {
                JsBarcode(svg, val, {
                    format: "CODE128",
                    width: 2,
                    height: 50,
                    displayValue: true,
                    fontSize: 12
                });
                wrap.show();
            } else {
                wrap.hide();
            }
        });

        // Show preview on page load if editing existing product
        window.addEventListener('load', function() {
            const input = document.getElementById('barcode-input');
            if (input && input.value.trim().length >= 4) {
                input.dispatchEvent(new Event('input'));
            }

            $('.variant-barcode-input').each(function() {
                $(this).trigger('input');
            });
        });

    });



    function StringToSlug(Text) {
        return Text.toLowerCase()
            .replace(/[^\w ]+/g, "")
            .replace(/ +/g, "-");
    }
</script>
@endpush