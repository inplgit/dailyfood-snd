<!-- BEGIN: Header-->
<?php
use App\Helpers\MasterFormsHelper;
?>

<style>
/* Dropdown */
.dropdown{display:inline-block;position:relative;}
.dd-button{display:inline-block;cursor:pointer;font-size:20px;}
.dd-input{display:none;}
.dd-menu{position:absolute;top:100%;border:1px solid #ccc;border-radius:4px;padding:0;margin:10px 0 0 -50px;box-shadow:0 0 6px 0 rgba(0,0,0,0.1);background-color:#ffffff;list-style-type:none;z-index:1;}
.dd-input + .dd-menu{display:none;}
.dd-input:checked + .dd-menu{display:block;}
.dd-menu li{padding:10px 20px;cursor:pointer;}
.dd-menu li a{display:block;margin:-10px -20px;padding:10px 20px;width:250px;border-bottom:1px solid #000000;z-index:1;letter-spacing:1px;color:#000;}
.dd-menu li.divider{padding:0;border-bottom:1px solid #cccccc;}
.badge.badge-up{position:absolute;top:-8px;right:-7px;min-width:1.429rem;min-height:1.429rem;display:flex;align-items:center;justify-content:center;font-size:0.786rem;line-height:0.786;padding-left:0.25rem;padding-right:0.25rem;}
.font-weight-bold{font-weight:500 !important;background:#eeeeee;}
.dd-menu li a span{color:#bd1717;font-size:11px;font-weight:900;}
.account-inner .davtar img{height:inherit !important;}
</style>


<nav class="header-navbar  align-items-center  floating-nav">
    <div class="container-fluid head-sh">
        <div class="headerwrap">
            <div class="row align-items-center">
                <div class="col-md-4 col-lg-4">
                    <div class="searchBox">
                        <div class="serch_input">
                            <input class="searchInput"type="text" name="" placeholder="Search">
                            <div class="but_search">
                                <button class="searchButton" href="#">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-4">
                    <div class="tim d">
                        <h3>11:20<span>AM</span></h3>
                        <p class="date">January 1, 2024</p>
                    </div>
                </div>

                <div class="col-md-4 col-lg-4">
                    <div class="meinbox">
                        <div class="profie">
                            <ul class="profile-admin d-flex">
                                <li>
                                    <div class="calender">
                                        <a href="#">
                                            <i class="fa-regular fa-calendar-days"></i>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <label class="dropdown">
                                        <div class="dd-button">
                                            <i class="fa-regular fa-bell"></i>
                                            @if (isset(auth()->user()->notifications) && auth()->user()->unreadNotifications()->count() > 0)
                                                <span class="badge rounded-pill bg-danger badge-up notificationCounterClass">{{auth()->user()->unreadNotifications()->count()}}</span>
                                            @endif
                                        </div>
                                        <input type="checkbox" class="dd-input" id="test">
                                        <ul class="dd-menu notification-data">
                                            @if (auth()->user()->notifications->isNotEmpty())
                                                @foreach (auth()->user()->notifications()->latest()->take(10)->get() as $notifications)
                                                    <li>
                                                        <a href="{{ route('notification.redirect', $notifications->id) }}" class="{{ $notifications->read_at ? '' : 'font-weight-bold' }}">
                                                            {{$notifications->data['message'] ?? 'No message available'}} <span>{{ $notifications->created_at->diffForHumans() }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li><a href="javascript:void(0);">No Notifactions available</a></li>
                                            @endif
                                        </ul>
                                    </label>
                                </li>


                                <li>
                                    <div class="pro-user d-flex">
                                        <div class="widget-content-left">
                                            <div class="profile-pic">
                                                <div class="profile">
                                                    <span class="avatar">
                                                        <img class="round"  src="{{ Auth::user()->image ? url('storage/app/public/profile/'.Auth::user()->image) : 'https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/avatars/1.png' }}" id="profile-image1" alt="avatar" height="40" width="40">
                                                    </span>
                                                    <form id="upload_profile" action="javascrip:void(0);" enctype="multipart/form-data">
                                                        @csrf
                                                        <input id="profile-image-upload" name="image" class="hidden" type="file" onchange="previewFile()" >
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="widget-content-left  ml-3 header-user-info">
                                            <div class="widget-heading">
                                                    <div class="user-nav d-sm-flex d-none">
                                                    <span class="user-name fw-bolder">{{ Auth::user()->name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="dropdown user-name-drop">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa-solid fa-angle-down"></i></a>
                                    <div class="account-information dropdown-menu">
                                        <div class="account-inner">
                                            <div class="davtar">
                                                <span class="avatar"> <img class="round" id="profile-image2" src="{{ Auth::user()->image ? url('storage/app/public/profile/'.Auth::user()->image) :'https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/avatars/1.png'}}" alt="avatar" > </span>
                                                <div class="content_profile">
                                                    <h5>{{ Auth::user()->name }}</h5>
                                                    <!-- <p>Bridging the Future of Industry.</p> -->
                                                    <p>{{ Auth::user()->email }}</p>
                                                    {{-- <p>Amaz@innovative-net.com</p> --}}
                                                </div>
                                            </div>

                                            <div class="main-heading">
                                                <ul class="list-unstyled" id="nav">
                                                    <li>
                                                        <a href="#" rel="assets/css/color-one.css">
                                                            <div class="color-one"></div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                    <a href="#" rel="assets/css/color-two.css">
                                                        <div class="color-two"></div>
                                                    </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" rel="assets/css/color-three.css">
                                                            <div class="color-three"></div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" rel="assets/css/color-four.css">
                                                            <div class="color-four"></div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" rel="assets/css/color-five.css">
                                                            <div class="color-five"></div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" rel="assets/css/color-six.css">
                                                            <div class="color-six"></div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" rel="assets/css/color-seven.css">
                                                            <div class="color-seven"></div>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="account-footer">
                                            <div class="butts">
                                                <ul>
                                                    <li>
                                                         <a href="{{ route('user.viewProfile' , Auth::user()->id) }}" class=""><i class="fa-solid fa-user"></i> View profile</a>
                                                    </li>
                                                    <li>
                                                         <a href="{{ route('users.index') }}" class=""><i class="fa-solid fa-users"></i> Users list</a>
                                                    </li>
                                                    <li>
                                                         <a href="{{ route('user.profileEdit') }}" class=""><i class="fa-solid fa-pencil"></i> Edit profile</a>
                                                    </li>
                                                    <li>
                                                        {{-- <a href="{{ route('logout') }}" class="sign_out ">Sign out</a> --}}
                                                        <a  class=""  href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa-solid fa-right-to-bracket"></i> Sign out</a>
                                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                            {{ csrf_field() }}
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid head-sh">
        <div class="col-md-12 col-lg-12">
            <div class="nav_home">
                <div class="nav navbar-nav">
                    <ul class="tmenu-list d">
                        <li class="active">
                            <a class="btn btn-primary primary_nav" href="{{ url('dashboard') }}">Dashboard</a></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- END: Header-->

<script>

document.addEventListener('click', function (event) {
    const dropdown = document.querySelector('.dropdown');
    const input = dropdown.querySelector('.dd-input');

    // Check if the clicked element is outside the dropdown
    if (!dropdown.contains(event.target)) {
        input.checked = false; // Uncheck the checkbox
    }
});


</script>
