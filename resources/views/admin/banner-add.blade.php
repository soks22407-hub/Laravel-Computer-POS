@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Banners</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('admin.slide.add') }}">
                        <div class="text-tiny">Banners</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">New Banner</div>
                </li>
            </ul>
        </div>
        <!-- new-category -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{route('admin.banner.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <fieldset class="name">
                    <div class="body-title">Title <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Title" name="title"
                        tabindex="0" value="{{old('title')}}" aria-required="true" required="">
                </fieldset>
                @error('title')
                <span class="alert alert-danger text-center">{{$message}}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Subtitle <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Subtitle" name="subtitle"
                        tabindex="0" value="{{old('subtitle')}}" aria-required="true" required="">
                </fieldset>
                @error('subtitle')
                <span class="alert alert-danger text-center">{{$message}}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Description <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Description" name="description"
                        tabindex="0" value="{{old('description')}}" aria-required="true" required="">
                </fieldset>
                @error('description')
                <span class="alert alert-danger text-center">{{$message}}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Type <span class="tf-color-1">*</span></div>
                    <div class="select flex-grow">
                        <select name="type" required>
                            <option value="">Select Type</option>
                            <option value="promotion" {{ old('type') == 'promotion' ? 'selected' : '' }}>
                                Promotion
                            </option>
                            <option value="discount" {{ old('type') == 'discount' ? 'selected' : '' }}>
                                Discount
                            </option>
                            <option value="new_product" {{ old('type') == 'new_product' ? 'selected' : '' }}>
                                New Product
                            </option>
                        </select>
                    </div>
                </fieldset>
                @error('type')
                <span class="alert alert-danger text-center">{{$message}}</span>
                @enderror
                <fieldset>
                    <div class="body-title">Upload images <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="display: none;">
                            <img src="sample.jpg" class="effect8" alt="" />
                        </div>
                        <div class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                <input type="file" id="myFile" name="image">
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('image')
                <span class="alert alert-danger text-center">{{$message}}</span>
                @enderror
                <fieldset class="category">
                    <div class="body-title">Status</div>
                    <div class="select flex-grow">
                        <select class="" name="status">
                            <option>Select</option>
                            <option value="1" @if (old('status')=="1" ) selected @endif>Active</option>
                            <option value="0" @if (old('status')=="0" ) selected @endif>Inactive</option>
                        </select>
                    </div>
                </fieldset>
                @error('status')
                <span class="alert alert-danger text-center">{{$message}}</span>
                @enderror
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
        <!-- /new-category -->
    </div>
    <!-- /main-content-wrap -->
</div>
@endsection

@push('scripts')
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
    });
</script>
@endpush