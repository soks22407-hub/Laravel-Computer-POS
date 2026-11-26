@extends('layouts.ceo')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Discount infomation</h3>
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
                    <a href="{{route ('ceo.discounts')}}">
                        <div class="text-tiny">Discounts</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Edit Discount</div>
                </li>
            </ul>
        </div>
        <div class="wg-box">
            <form class="form-new-product form-style-1" method="POST" action="{{ route('ceo.discount.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{$discount->id}}" />
                <fieldset class="name">
                    <div class="body-title">Discount Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Discount Name" name="name"
                        tabindex="0" value="{{$discount->name}}" aria-required="true" required="">
                </fieldset>
                @error('name') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="category">
                    <div class="body-title">Discount Type</div>
                    <div class="select flex-grow">
                        <select class="" name="type">
                            <option value="">Select</option>
                            <option value="fixed" {{$discount->type=='fixed' ? 'selected':''}}>Fixed</option>
                            <option value="percent" {{$discount->type=='percent' ? 'selected':''}}>Percent</option>
                        </select>
                    </div>
                </fieldset>
                @error('type') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="name">
                    <div class="body-title">Value <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Discount Value" name="value"
                        tabindex="0" value="{{$discount->value}}" aria-required="true" required="">
                </fieldset>
                @error('value') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="name">
                    <div class="body-title">Min Amount <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Min Amount"
                        name="min_amount" tabindex="0" value="{{$discount->min_amount}}" aria-required="true"
                        required="">
                </fieldset>
                @error('min_amount') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="name">
                    <div class="body-title">Start Date <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="date" placeholder="Start Date"
                        name="start_date" tabindex="0" value="{{ $discount->start_date ? \Carbon\Carbon::parse($discount->start_date)->format('Y-m-d') : '' }}" aria-required="true"
                        required="">
                </fieldset>
                @error('start_date') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="name">
                    <div class="body-title">End Date <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="date" placeholder="End Date"
                        name="end_date" tabindex="0" value="{{ $discount->end_date ? \Carbon\Carbon::parse($discount->end_date)->format('Y-m-d') : '' }}" aria-required="true"
                        required="">
                </fieldset>
                @error('end_date') <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="category">
                    <div class="body-title">Status</div>
                    <div class="select flex-grow">
                        <select class="" name="status" required>
                            <option value="">Select</option>
                            <option value="1" {{ $discount->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $discount->status == 0 ? 'selected' : '' }}>Inactive</option>
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
    </div>
</div>
@endsection