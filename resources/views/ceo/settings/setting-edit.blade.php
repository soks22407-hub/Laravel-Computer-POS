@extends('layouts.ceo')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Setting infomation</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route ('ceo.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{route ('ceo.settings')}}">
                        <div class="text-tiny">Settings</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Edit Setting</div>
                </li>
            </ul>
        </div>
    
        <div class="wg-box">
            <h4 class="mb-4">⚙️ Update Settings</h4>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('ceo.settings.update', $settings->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{$settings->id}}" />
                <div class="mb-3">
                    <div class="body-title">Site Name <span class="tf-color-1">*</span></div>
                    <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings->site_name ?? '') }}">
                </div>
                @error('site_name') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <div class="mb-3">
                    <div class="body-title">Email <span class="tf-color-1">*</span></div>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $settings->email ?? '') }}">
                </div>
                @error('email') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <div class="mb-3">
                    <div class="body-title">Phone <span class="tf-color-1">*</span></div>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $settings->phone ?? '') }}">
                </div>
                @error('phone') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset>
                    <div class="body-title">Upload logos <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="display:none">
                            <img src="upload-1.html" class="effect8" alt="">
                        </div>
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                <input type="file" id="myFile" name="logo" accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('logo') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <br />
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">💾 Save Changes</button>
                </div>
            </form>
        </div>
    </div>
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