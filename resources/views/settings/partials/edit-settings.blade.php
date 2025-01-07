<form method="post" action="{{ route('settings.post', $setting->id) }}" enctype="multipart/form-data">
    @csrf
    <div class="example-box-wrapper">
        <div class="form-horizontal bordered-row">

            <div class="form-group bg-khaki">
                <h3 class="control-label col-sm-2 title-hero">
                    {{ trans('core.general_settings') }}
                </h3>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2">
                    {{ trans('core.shop_name') }}
                </label>
                <div class="col-sm-4 {{ $errors->has('site_name') ? 'has-error' : '' }}">
                    <input type="text" name="site_name" value="{{ old('site_name', $setting->site_name) }}" class="form-control">
                </div>

                <label class="control-label col-sm-2">
                    Tagline
                </label>
                <div class="col-sm-4">
                    <input type="text" name="slogan" value="{{ old('slogan', $setting->slogan) }}" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2">
                    {{ trans('core.phone') }}
                </label>
                <div class="col-sm-4 {{ $errors->has('phone') ? 'has-error' : '' }}">
                    <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" class="form-control">
                </div>

                <label class="control-label col-sm-2">
                    {{ trans('core.email') }}
                </label>
                <div class="col-sm-4 {{ $errors->has('email') ? 'has-error' : '' }}">
                    <input type="text" name="email" value="{{ old('email', $setting->email) }}" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2">
                    {{ trans('core.shop_address') }}
                </label>
                <div class="col-sm-10 {{ $errors->has('address') ? 'has-error' : '' }}">
                    <textarea name="address" class="form-control" rows="3">{{ old('address', $setting->address) }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2">
                    {{ trans('core.shop_owner') }}
                </label>
                <div class="col-sm-4">
                    <input type="text" name="owner_name" value="{{ old('owner_name', $setting->owner_name) }}" class="form-control">
                </div>

                <label class="control-label col-sm-2">
                    {{ trans('core.currency') }}
                </label>
                <div class="col-sm-4">
                    <input type="text" name="currency_code" value="{{ old('currency_code', $setting->currency_code) }}" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2">
                    {{ trans('core.theme') }}
                </label>
                <div class="col-sm-4">
                    <select name="theme" class="form-control">
                        <option value="bg-primary" {{ old('theme', $setting->theme) == 'bg-primary' ? 'selected' : '' }}>Pacific Blue</option>
                        <option value="bg-green" {{ old('theme', $setting->theme) == 'bg-green' ? 'selected' : '' }}>Green</option>
                        <option value="bg-red" {{ old('theme', $setting->theme) == 'bg-red' ? 'selected' : '' }}>Red</option>
                        <option value="bg-blue" {{ old('theme', $setting->theme) == 'bg-blue' ? 'selected' : '' }}>Blue</option>
                        <option value="bg-warning" {{ old('theme', $setting->theme) == 'bg-warning' ? 'selected' : '' }}>Orange</option>
                        <option value="bg-purple" {{ old('theme', $setting->theme) == 'bg-purple' ? 'selected' : '' }}>Purple</option>
                        <option value="bg-black" {{ old('theme', $setting->theme) == 'bg-black' ? 'selected' : '' }}>Black</option>
                        <option value="bg-gradient-1" {{ old('theme', $setting->theme) == 'bg-gradient-1' ? 'selected' : '' }}>Moderate Azure</option>
                        <option value="bg-gradient-2" {{ old('theme', $setting->theme) == 'bg-gradient-2' ? 'selected' : '' }}>Strong Spring Green</option>
                        <option value="bg-gradient-3" {{ old('theme', $setting->theme) == 'bg-gradient-3' ? 'selected' : '' }}>Magenta-pink</option>
                        <option value="bg-gradient-4" {{ old('theme', $setting->theme) == 'bg-gradient-4' ? 'selected' : '' }}>Desaturated Cyan</option>
                        <option value="bg-gradient-5" {{ old('theme', $setting->theme) == 'bg-gradient-5' ? 'selected' : '' }}>Strong Azure</option>
                        <option value="bg-gradient-6" {{ old('theme', $setting->theme) == 'bg-gradient-6' ? 'selected' : '' }}>Vivid Cyan</option>
                        <option value="bg-gradient-7" {{ old('theme', $setting->theme) == 'bg-gradient-7' ? 'selected' : '' }}>Deep Cyan</option>
                        <option value="bg-gradient-8" {{ old('theme', $setting->theme) == 'bg-gradient-8' ? 'selected' : '' }}>Strong Cornflower Blue</option>
                        <option value="bg-gradient-9" {{ old('theme', $setting->theme) == 'bg-gradient-9' ? 'selected' : '' }}>Strong Arctic Blue</option>
                    </select>
                </div>

                <label class="control-label col-sm-2">
                    {{ trans('core.dashboard_style') }}
                </label>
                <div class="col-sm-4">
                    <select name="dashboard_style" class="form-control">
                        <option value="chart-box" {{ old('dashboard_style', $setting->dashboard_style) == 'chart-box' ? 'selected' : '' }}>Chart Box</option>
                        <option value="tile-box" {{ old('dashboard_style', $setting->dashboard_style) == 'tile-box' ? 'selected' : '' }}>Tile Box</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">
                    {{ trans('core.logo') }}
                </label>
                <div class="col-sm-10">
                    <input type="file" name="image" id="file">
                    <br>
                    <small>
                        Logo size should be (width=190px) x (height=34px).
                    </small>
                </div>
            </div>

            <div class="form-group bg-khaki">
                <h3 class="control-label col-sm-3 title-hero">
                    {{ trans('core.sell_n_purchase_settings') }}
                </h3>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2">
                    {{ trans('core.invoice_tax') }}
                </label>
                <div class="col-sm-4">
                    <select name="invoice_tax" class="form-control" id="invoice_tax">
                        <option value="0" {{ old('invoice_tax', $setting->invoice_tax) == '0' ? 'selected' : '' }}>Disable</option>
                        <option value="1" {{ old('invoice_tax', $setting->invoice_tax) == '1' ? 'selected' : '' }}>Enable</option>
                    </select>
                </div>

                <div id="invoice_tax_rate">
                    <label class="control-label col-sm-2">
                        {{ trans('core.invoice_tax_rate') }}
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control" name="invoice_tax_id">
                            @foreach($taxes as $tax)
                                <option value="{{ $tax->id }}" {{ old('invoice_tax_id', $setting->invoice_tax_id) == $tax->id ? 'selected' : '' }}>
                                    {{ $tax->name }}
                                </option>
                            @endforeach
                        </select>
                        <span>Add VAT Rate?</span>
                        <a href="{{ route('tax.index') }}" style="text-decoration: underline; padding: 10px; color: blue;">Click Here</a>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2">
                    {{ trans('core.enable_purchaser') }}
                </label>
                <div class="col-sm-4">
                    <select name="enable_purchaser" class="form-control">
                        <option value="0" {{ old('enable_purchaser', $setting->enable_purchaser) == '0' ? 'selected' : '' }}>Disable</option>
                        <option value="1" {{ old('enable_purchaser', $setting->enable_purchaser) == '1' ? 'selected' : '' }}>Enable</option>
                    </select>
                </div>

                <label class="control-label col-sm-2">
                    {{ trans('core.enable_customer') }}
                </label>
                <div class="col-sm-4">
                    <select name="enable_customer" class="form-control">
                        <option value="0" {{ old('enable_customer', $setting->enable_customer) == '0' ? 'selected' : '' }}>Disable</option>
                        <option value="1" {{ old('enable_customer', $setting->enable_customer) == '1' ? 'selected' : '' }}>Enable</option>
                    </select>
                </div>
            </div>

            <div class="form-group bg-khaki">
                <h3 class="control-label col-sm-2 title-hero">
                    {{ trans('core.invoice_settings') }}
                </h3>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2">
                    {{ trans('core.vat_no') }}
                </label>
                <div class="col-sm-10">
                    <input type="text" name="vat_no" value="{{ old('vat_no', $setting->vat_no) }}" class="form-control">
                </div>
            </div>

            <div class="form-group bg-khaki">
                <h3 class="control-label col-sm-2 title-hero">
                    {{ trans('core.pos_settings') }}
                </h3>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2">
                    {{ trans('core.pos_footer_text') }}
                </label>
                <div class="col-sm-10">
                    <textarea name="pos_invoice_footer_text" class="form-control" rows="2">{{ old('pos_invoice_footer_text', $setting->pos_invoice_footer_text) }}</textarea>
                </div>
            </div>

            @if(auth()->user()->can('settings.manage'))
                <div class="bg-default content-box text-center pad20A mrg25T">
                    <button class="btn btn-lg btn-primary" type="submit">
                        {{ trans('core.save') }}
                    </button>
                </div>
            @endif
        </div>
    </div>
</form>
