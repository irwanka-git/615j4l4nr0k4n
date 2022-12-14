<li class="nav-item dropdown">
	<a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
		<i class="align-middle" data-feather="settings"></i>
		</a>

	<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
		<img src="{{ asset('img/user.png') }}" class="avatar img-fluid rounded-circle me-1" alt="User" /> <span class="text-dark">{{ Auth::user()->name }}</span>
		</a>
	<div class="dropdown-menu dropdown-menu-end">
		<a class="dropdown-item" href="{{url('ganti-password')}}">Ganti Pawword</a>
		<a class="dropdown-item" href="{{url('logout')}}">Log Out (Keluar)</a>
	</div>
</li>