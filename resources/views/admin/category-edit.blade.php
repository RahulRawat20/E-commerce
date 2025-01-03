@extends('layouts.admin')
@section('title', ' Admin / Edit Category')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Category infomation</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('admin.categories')}}">
                        <div class="text-tiny">Categories</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Edit category</div>
                </li>
            </ul>
        </div>
        <!-- new-category -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{ route('admin.category.update')}}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{$category->id}}">
                <fieldset class="name">
                    <div class="body-title">category Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="category name" name="name"
                        tabindex="0" value="{{$category->name}}" aria-required="true" required="">
                </fieldset>
                @error('name')<span class="alert alert-danger">{{ $message }}</span>@enderror
                    
                <fieldset class="name">
                    <div class="body-title">category Slug <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="category Slug" name="slug"
                        tabindex="0" value="{{$category->slug}}" aria-required="true" required="">
                </fieldset>
                @error('slug')<span class="alert alert-danger">{{ $message }}</span>@enderror
                <fieldset>
                    <div class="body-title">Upload images <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        @if ($category->image) 
                        <div class="item" id="imgpreview" style="display:">
                            <img src="{{ asset('uploads/categories')}}/{{$category->image}}" class="effect8" alt="">
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
                {{-- @error('image')<span class="alert alert-danger">{{ $message }}</span>@enderror --}}

                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    $(function(){
        // Handling file input change
        $("#myFile").on("change", function(e){
            const photoInp = $("#myFile");
            const [file] = this.files; // Destructure to get the file
            if(file) {
                // If a file is selected, show the preview
                $("#imgpreview img").attr('src', URL.createObjectURL(file));
                $("#imgpreview").show();  // Show the preview div
            } else {
                // If no file is selected, hide the preview
                $("#imgpreview").hide();
            }
        });

        // Handling name input change to generate slug
        $("input[name='name']").on("change", function(){
            $("input[name='slug']").val(stringToSlug($(this).val()));  // Convert the name to a slug
        });

    });

    // Function to convert text to slug
    function stringToSlug(text) {
        return text.toLowerCase()  // Convert the string to lowercase
        .replace(/[^\w\s-]/g, '')  // Remove all non-word characters (except spaces and hyphens)
        .replace(/[\s-]+/g, '-')  // Replace spaces or hyphens with a single hyphen
        .replace(/^-+|-+$/g, '');  // Remove any leading or trailing hyphens
    }
</script>

@endpush