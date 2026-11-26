@extends('layouts.app')
@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="contact-us container">
        <div class="mw-930">
            <h2 class="page-title">About US</h2>
        </div>

        <div class="about-us__content pb-5 mb-5">
            <p class="mb-5">
                <img loading="lazy" class="w-100 h-auto d-block" src="assets/images/about/shop-computer.jpg" width="1410"
                    height="250" alt="" />
            </p>
            <div class="mw-930">
                <h3 class="mb-4">OUR STORY</h3>
                <p class="fs-6 fw-medium mb-4">Solaire was founded to solve a common problem for gamers: finding high-quality computers and gaming gear at fair prices without dealing with scalpers or unreliable sellers. 
                    What started as a small community on Discord quickly grew into a trusted marketplace for gamers nationwide.</p>
                <p class="mb-4">Our journey began during a time of limited availability and high demand for PC components. 
                    Seeing the frustration of gamers trying to build or upgrade their rigs, our founder decided to create a platform that makes buying and selling gaming gear simple, 
                    transparent, and safe. Today, Jawa connects gamers, custom PC builders, and enthusiasts, providing a space to trade gear, share knowledge, 
                    and support the gaming community.</p>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5 class="mb-3">Our Mission</h5>
                        <p class="mb-3">Our mission is to create a trusted marketplace where gamers can buy and sell high-quality computers and gaming gear at fair prices. 
                            We aim to empower both buyers and sellers, eliminate scalpers, and make the process of finding, selling, 
                            or upgrading gaming equipment simple, safe, and transparent.</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Our Vision</h5>
                        <p class="mb-3">Our vision is to become the leading community-driven marketplace for gaming computers and gear, 
                            where gamers nationwide can confidently buy, sell, and upgrade their equipment. 
                            We strive to build a platform that supports innovation, fosters trust, 
                            and connects enthusiasts to the products they need to enjoy the ultimate gaming experience.</p>
                    </div>
                </div>
            </div>
            <div class="mw-930 d-lg-flex align-items-lg-center">
                <div class="image-wrapper col-lg-6">
                    <a href="{{ $map }}" target="_blank">
                        <img loading="lazy" class="w-100 h-auto d-block" 
                            src="{{ asset('assets/images/about/map.jpg') }}" 
                            style="width:400px; height:300px; display:block; margin:0 auto;">
                    </a>
                </div>
                <div class="content-wrapper col-lg-6 px-lg-4">
                    <h5 class="mb-3">The Company</h5>
                    <p>Solaire is a marketplace dedicated to gamers and PC enthusiasts. 
                        We specialize in buying and selling high-quality gaming computers, components, and peripherals.
                        Our platform connects sellers who have reliable gear with buyers looking for fair prices, 
                        eliminating scalpers and shady listings.</p>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection