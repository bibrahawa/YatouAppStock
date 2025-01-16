<!-- Main Footer -->
<div class="footer bg-black">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
       Developpé par
        <a href="https://www.yatou.com" target="_blank">
       		<b>{{ settings('owner_name') }}</b>
       	</a>. &nbsp; | &nbsp; v2.0.0
    </div>
    <!-- Default to the left -->
    <strong>{{trans('core.copyright')}} &copy; {{ date('Y') }}
    	<a href="#">
    		{{ settings('site_name') }}
    	</a>
    </strong>
</div>
