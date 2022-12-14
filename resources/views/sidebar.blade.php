<?php
	$path = Request::segment(1);
	$sesion_menu = session('menu_app');
	$menu_session  = json_decode(($sesion_menu));
?>

	<li class="sidebar-item">
      <a data-bs-target="#mnu" data-bs-toggle="collapse" class="sidebar-link collapsed">
        <i class="align-middle" data-feather="grid"></i> <span class="align-middle">Menu</span>
      </a>
    	<ul id="mnu" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
    	  @foreach($menu_session as $menu)
	      <li class="sidebar-item @if($path==$menu->url) active @endif"><a href="{!! url($menu->url) !!}" 
	      	class="sidebar-link">{!! $menu->nama_menu !!}</a></li>
	      @endforeach
	   	</ul>
  </li>
 