<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="author" content="solaire" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/animate.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/animation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-select.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('font/fonts.css')}}">
    <link rel="stylesheet" href="{{ asset('icon/style.css')}}">
    <link rel="shortcut icon" href="{{ asset('images/logo2.png.png')}}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('images/logo2.png.png')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/sweetalert.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    @stack("styles")
</head>

<body class="body">
    <div id="wrapper">
        <div id="page" class="">
            <div class="layout-wrap">

                <!-- <div id="preload" class="preload-container">
    <div class="preloading">
        <span></span>
    </div>
</div> -->

                <style>
                    .product-item {
                        display: flex;
                        align-items: center;
                        justify-content: flex-start;
                        gap: 15px;
                        transition: all 0.3s ease;
                        padding-right: 5px;
                    }

                    .product-item .image {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 50px;
                        height: 50px;
                        gap: 10px;
                        flex-shrink: 0;
                        padding: 5px;
                        border-radius: 10px;
                        background: #EFF4F8;
                    }

                    #box-content-search li {
                        list-style: none;
                    }

                    #box-content-search .product-item {
                        margin-bottom: 10px;
                    }

                    .logo__image {
                        width: 80px;
                        /* control size */
                        height: 70px;
                        /* make it square */
                        object-fit: contain;
                        /* keep full image */
                    }

                    .box-logo {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }

                    .header-user .image img {
                        width: 55px;
                        height: 55px;
                        border-radius: 50%;
                        object-fit: cover;
                        aspect-ratio: 1 / 1;
                        /* ← forces perfect square before rounding */
                        flex-shrink: 0;
                        /* ← prevents it from being squished */
                    }

                    .header-user .image {
                        width: 55px;
                        height: 55px;
                        flex-shrink: 0;
                        /* ← container also shouldn't shrink */
                    }

                    .header-user.wg-user {
                        gap: 10px;
                    }
                </style>

                <div class="section-menu-left">
                    <div class="box-logo">
                        <a href="{{route('employee.index')}}" id="site-logo-inner">
                            <img src="{{ asset('uploads/setting/' . $globalSettings->logo) }}" class="logo__image d-block" />
                        </a>
                        <div class="button-show-hide">
                            <i class="icon-menu-left"></i>
                        </div>
                    </div>
                    <div class="center">
                        <div class="center-item">
                            <div class="center-heading">Main Home</div>
                            <ul class="menu-list">
                                <li class="menu-item">
                                    <a href="{{route('employee.index')}}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Dashboard</div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="center-item">
                            <ul class="menu-list">
                                @if(auth()->user()->can_do('products', 'view'))
                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-shopping-cart"></i></div>
                                        <div class="text">Products</div>
                                    </a>
                                    <ul class="sub-menu">
                                        @if(auth()->user()->can_do('products', 'add'))
                                        <li class="sub-menu-item">
                                            <a href="{{route('employee.product.add')}}" class="">
                                                <div class="text">Add Product</div>
                                            </a>
                                        </li>
                                        @endif
                                        <li class="sub-menu-item">
                                            <a href="{{route('employee.products')}}" class="">
                                                <div class="text">Products</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @endif

                                @if(auth()->user()->can_do('orders', 'view'))
                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-file-plus"></i></div>
                                        <div class="text">Order</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{ route('employee.orders') }}" class="">
                                                <div class="text">Orders</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @endif

                                @if(auth()->user()->can_do('stock_alert', 'view'))
                                <li class="menu-item">
                                    <a href="{{ route('employee.stock.alert') }}" class="{{ request()->routeIs('employee.stock.alert') ? 'active' : '' }}">
                                        <div class="icon">
                                            <i class="icon-package"></i>
                                        </div>
                                        <div class="text">Stock Alert
                                            @php $lowCount = \App\Models\Product::where('quantity', '<=', 5)->count(); @endphp
                                                @if($lowCount > 0)
                                                <span class="badge bg-danger ms-1">{{ $lowCount }}</span>
                                                @endif
                                        </div>
                                    </a>
                                </li>
                                @endif

                                @if(auth()->user()->can_do('purchase_requests', 'view'))
                                <li class="menu-item">
                                    <a href="{{ route('employee.purchase-requests.index') }}" class="{{ request()->routeIs('employee.purchase-requests*') ? 'active' : '' }}">
                                        <div class="icon"><i class="icon-clipboard"></i></div>
                                        <div class="text">Purchase Requests</div>
                                    </a>
                                </li>
                                @endif

                                @if(auth()->user()->can_do('contacts', 'view'))
                                <li class="menu-item">
                                    <a href="{{ route('employee.contacts') }}" class="">
                                        <div class="icon"><i class="icon-mail"></i></div>
                                        <div class="text">Messages</div>
                                    </a>
                                </li>
                                @endif

                                @if(auth()->user()->can_do('pos', 'view'))
                                <li class="menu-item">
                                    <a href="{{ route('employee.pos') }}"
                                        class="{{ request()->routeIs('employee.pos*') ? 'active' : '' }}">
                                        <div class="icon"><i class="icon-shopping-cart"></i></div>
                                        <div class="text">Point of Sale</div>
                                    </a>
                                </li>
                                @endif

                                <li class="menu-item">
                                    <form method="POST" action="{{route('logout')}}" id="logout-form">
                                        @csrf
                                        <a href="{{route('logout')}}" class="" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                            <div class="icon"><i class="fa-solid fa-right-from-bracket"></i></div>
                                            <div class="text">Logout</div>
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="section-content-right">

                    <div class="header-dashboard">
                        <div class="wrap">
                            <div class="header-left">
                                <a href="index-2.html">
                                    <img class="" id="logo_header_mobile" alt="" src="{{ asset('images/logo/logo1.png')}}"
                                        data-light="{{ asset('images/logo/logo1.png')}}" data-dark="{{ asset('images/logo/logo1.png')}}"
                                        data-width="154px" data-height="52px" data-retina="{{ asset('images/logo/logo1.png')}}">
                                </a>
                                <div class="button-show-hide">
                                    <i class="icon-menu-left"></i>
                                </div>


                                <form class="form-search flex-grow">
                                    <fieldset class="name">
                                        <input type="text" placeholder="Search here..." class="show-search" name="name" id="search-input"
                                            tabindex="2" value="" aria-required="true" required="" autocomplete="off">
                                    </fieldset>
                                    <div class="button-submit">
                                        <button class="" type="submit"><i class="icon-search"></i></button>
                                    </div>
                                    <div class="box-content-search">
                                        <ul id="box-content-search"></ul>
                                    </div>
                                </form>

                            </div>
                            <div class="header-grid">

                                <div class="popup-wrap message type-header">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="header-item">
                                                <span class="text-tiny">{{ $unreadCount }}</span>
                                                <i class="icon-bell"></i>
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end has-content"
                                            aria-labelledby="dropdownMenuButton2">
                                            <li>
                                                <h6>Notifications</h6>
                                            </li>
                                            <!-- <li>
                                                <div class="message-item item-1">
                                                    <div class="image">
                                                        <i class="icon-noti-1"></i>
                                                    </div>
                                                    <div>
                                                        <div class="body-title-2">Discount available</div>
                                                        <div class="text-tiny">Morbi sapien massa, ultricies at rhoncus
                                                            at, ullamcorper nec diam</div>
                                                    </div>
                                                </div>
                                            </li> -->
                                            <!-- <li>
                                                <div class="message-item item-2">
                                                    <div class="image">
                                                        <i class="icon-noti-2"></i>
                                                    </div>
                                                    <div>
                                                        <div class="body-title-2">Account has been verified</div>
                                                        <div class="text-tiny">Mauris libero ex, iaculis vitae rhoncus
                                                            et</div>
                                                    </div>
                                                </div>
                                            </li> -->
                                            <!-- <li>
                                                <div class="message-item item-3">
                                                    <div class="image">
                                                        <i class="icon-noti-3"></i>
                                                    </div>
                                                    <div>
                                                        <div class="body-title-2">Order shipped successfully</div>
                                                        <div class="text-tiny">Integer aliquam eros nec sollicitudin
                                                            sollicitudin</div>
                                                    </div>
                                                </div>
                                            </li> -->
                                            @foreach($notifications as $note)
                                            <li>
                                                <div class="message-item {{ !$note->is_read ? 'item-4' : '' }}">
                                                    <div class="image">
                                                        <i class="icon-noti-4"></i>
                                                    </div>
                                                    <div>
                                                        <div class="body-title-2">
                                                            {{ $note->title }}: <span>ID {{ $note->id }}</span>
                                                        </div>
                                                        <div class="text-tiny">{{ $note->message }}</div>
                                                    </div>
                                                </div>
                                            </li>
                                            @endforeach
                                            <li>
                                                <a href="{{ route('employee.inbox') }}" class="tf-button w-full">View all</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="popup-wrap user type-header">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="header-user wg-user">
                                                <span class="image">
                                                    <img
                                                        src="{{ Auth::user()->profile_photo ? asset('storage/profile_photos/' . Auth::user()->profile_photo) : asset('images/default_photo.png') }}"
                                                        alt="{{ Auth::user()->name }}">
                                                </span>
                                                <span class="flex flex-column">
                                                    <span class="body-title mb-1">{{ Auth::user()->name }}</span>
                                                    <span class="text-tiny">{{ Auth::user()->utype ?? 'EMP' }}</span>
                                                </span>
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end has-content"
                                            aria-labelledby="dropdownMenuButton3">
                                            <li>
                                                <a href="{{route('employee.profile')}}" class="user-item">
                                                    <div class="icon">
                                                        <i class="icon-user"></i>
                                                    </div>
                                                    <div class="body-title-2">Account</div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('employee.inbox') }}" class="user-item d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon me-2">
                                                            <i class="icon-mail"></i>
                                                        </div>
                                                        <div class="body-title-2">Inbox</div>
                                                    </div>

                                                    @if(isset($unreadCount) && $unreadCount > 0)
                                                    <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                                                    @endif
                                                </a>
                                            </li>
                                            @if(auth()->user()->can_do('tasks', 'view'))
                                            <li>
                                                <a href="{{ route('employee.tasks') }}" class="user-item">
                                                    <div class="icon">
                                                        <i class="icon-file-text"></i>
                                                    </div>
                                                    <div class="body-title-2">Taskboard</div>
                                                </a>
                                            </li>
                                            @endif

                                            <li>
                                                <a href="{{ route('home.index') }}" class="user-item">
                                                    <div class="icon">
                                                        <i class="icon-home"></i>
                                                    </div>
                                                    <div class="body-title-2">Return Home</div>
                                                </a>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{route('logout')}}" id="logout-form">
                                                    @csrf
                                                    <a href="{{route('logout')}}" class="user-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                                        <div class="icon">
                                                            <i class="icon-log-out"></i>
                                                        </div>
                                                        <div class="body-title-2">Log out</div>
                                                    </a>
                                                </form>

                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="main-content">
                        @yield('content')
                        
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js')}}"></script>
    <script src="{{ asset('js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('js/bootstrap-select.min.js')}}"></script>
    <script src="{{ asset('js/sweetalert.min.js')}}"></script>
    <script src="{{ asset('js/apexcharts/apexcharts.js')}}"></script>
    <script>
        $(function() {
            $("#search-input").on("keyup", function() {
                var searchQuery = $(this).val();
                if (searchQuery.length > 2) {
                    $.ajax({
                        type: "GET",
                        url: "{{route('employee.search')}}",
                        data: {
                            query: searchQuery
                        },
                        dataType: 'json',
                        success: function(data) {
                            $("#box-content-search").html('');
                            $.each(data, function(index, item) {
                                var url = "{{route('employee.product.edit', ['id' => 'product_id'])}}";
                                var link = url.replace('product_id', item.id);

                                $("#box-content-search").append(`
                                    <li class="product-item">  
                                    <a href="${link}" class="d-flex items-center gap14 mb-10"> 
                                        <div class="image no-bg">
                                        <img src="{{ asset('uploads/products/Thumbnails') }}/${item.image}" alt="${item.name}">
                                        </div>
                                        <div class="flex items-center justify-between gap20 flex-grow">
                                        <div class="name">
                                            <span class="body-text">${item.name}</span>
                                        </div>
                                        </div>
                                    </a>
                                    </li>
                                    <li>
                                    <div class="divider"></div>
                                    </li>
                                `);
                            });
                        }
                    });
                }
            });



            const savedCount = localStorage.getItem('lastUnreadCount');
            let lastUnreadCount = savedCount !== null ? parseInt(savedCount) : parseInt(document.getElementById('unread-badge').dataset.count || 0);

            function playSound() {
                const audio = document.getElementById('notif-sound');
                if (audio) {
                    audio.currentTime = 0;
                    audio.play().catch(() => {});
                }
            }

            function updateBadge(count) {
                const badge = document.getElementById('unread-badge');
                if (badge) badge.textContent = count;

                const inboxBadge = document.querySelector('.inbox-badge');
                if (inboxBadge) {
                    inboxBadge.textContent = count;
                    inboxBadge.style.display = count > 0 ? 'inline-block' : 'none';
                }
            }

            function checkNotifications() {
                $.get('<?= route("employee.inbox.count") ?>', function(data) {
                    const newCount = parseInt(data.count) || 0;
                    const saved = localStorage.getItem('lastUnreadCount');
                    const savedCount = saved !== null ? parseInt(saved) : newCount;

                    if (newCount > savedCount) {
                        playSound();
                        $('.icon-bell').addClass('bell-shake');
                        setTimeout(() => $('.icon-bell').removeClass('bell-shake'), 1000);

                        if (Notification.permission === 'granted') {
                            new Notification('📦 New Order Notification', {
                                body: `You have ${newCount} unread notifications.`,
                                icon: '<?= asset("images/logo2.png.png") ?>'
                            });
                        }
                    }

                    localStorage.setItem('lastUnreadCount', newCount);
                    lastUnreadCount = newCount;
                    updateBadge(newCount);
                });
            }

            // Unlock audio on first interaction
            let audioUnlocked = false;
            $(document).one('click keydown touchstart', function() {
                const audio = document.getElementById('notif-sound');
                if (audio && !audioUnlocked) {
                    audio.play().then(() => {
                        audio.pause();
                        audio.currentTime = 0;
                        audioUnlocked = true;
                    }).catch(() => {});
                }
            });

            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }

            // Check immediately on page load, then every 10 seconds
            checkNotifications();
            setInterval(checkNotifications, 10000);
        });
        // Highlight active menu item based on current URL
        $(document).ready(function() {
            const currentUrl = window.location.href;

            // Match exact links
            $('.menu-list a[href]').each(function() {
                const href = $(this).attr('href');
                if (href && href !== 'javascript:void(0);' && currentUrl.includes(href)) {
                    $(this).addClass('active');
                    // If it's a sub-menu item, open the parent
                    const parentLi = $(this).closest('.menu-item.has-children');
                    if (parentLi.length) {
                        parentLi.addClass('active');
                        parentLi.find('> .sub-menu').show();
                    }
                }
            });

            // Submenu toggle: only toggle, don't navigate
            $('.menu-item.has-children > a.menu-item-button').off('click').on('click', function(e) {
                e.preventDefault();
                const parentLi = $(this).closest('.menu-item.has-children');
                const isActive = parentLi.hasClass('active');

                // Close all other open submenus
                $('.menu-item.has-children').not(parentLi).removeClass('active')
                    .find('> .sub-menu').slideUp(100);

                // Toggle current
                if (isActive) {
                    parentLi.removeClass('active');
                    parentLi.find('> .sub-menu').slideUp(100);
                } else {
                    parentLi.addClass('active');
                    parentLi.find('> .sub-menu').slideDown(100);
                }
            });
        });
    </script>
    <script src="{{ asset('js/main.js')}}"></script>

    @stack("scripts")
</body>

</html>