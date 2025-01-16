<div class="form-horizontal bordered-row">
    <div class="form-group">
        <label class="control-label col-sm-3">{{ __('core.shop_name') }}</label>
        <div class="col-sm-7">
            <span class="form-control" disabled>{{ $setting->site_name }}</span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3">{{ __('core.shop_slogan') }}</label>
        <div class="col-sm-7">
            <span class="form-control" disabled>{{ $setting->slogan }}</span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3">{{ __('core.shop_address') }}</label>
        <div class="col-sm-7">
            <span class="form-control" disabled>{{ $setting->address }}</span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3">{{ __('core.phone') }}</label>
        <div class="col-sm-7">
            <span class="form-control" disabled>{{ $setting->phone }}</span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3">{{ __('core.email') }}</label>
        <div class="col-sm-7">
            <span class="form-control" disabled>{{ $setting->email }}</span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3">{{ __('core.shop_owner') }}</label>
        <div class="col-sm-7">
            <span class="form-control" disabled>{{ $setting->owner_name }}</span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3">{{ __('core.currency') }}</label>
        <div class="col-sm-7">
            <span class="form-control" disabled>{{ $setting->currency_code }}</span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3">{{ __('core.alert_range') }}</label>
        <div class="col-sm-7">
            <span class="form-control" disabled>{{ $setting->alert_quantity }}</span>
        </div>
    </div>

    @if($setting->site_logo)
        <div class="form-group">
            <label class="control-label col-sm-3">{{ __('core.logo') }}</label>
            <div class="col-sm-7">
                <img src="{{ asset('uploads/site/' . $setting->site_logo) }}" class="img-responsive" style="height: 50px; width:auto;" alt="User Image"/>
            </div>
        </div>
    @endif
</div>
