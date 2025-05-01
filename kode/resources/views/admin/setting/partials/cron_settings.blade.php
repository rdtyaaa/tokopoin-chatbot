
<div class="border rounded">
    
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{$tab}}
        </h5>
    </div>
    
    <div class="p-3">
        <form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">

            @csrf

            <div class="row g-4 mb-4">
    
               
                <div class="col-lg-12">
                    <label for="status_expiry" class="form-label">
                        {{translate('Product new status expiry')}}
                        <span class="text-danger">
                            ({{translate('Days')}})
                        </span>
                    </label>
                    <input type="number" name="site_settings[status_expiry]" class="form-control" id="status_expiry" name="status_expiry" value="{{site_settings('status_expiry')}}" >
                </div>

                <div class="col-6">
                    <label for="cron_url" class="form-label">
                        {{translate('Cron Job URL')}}
                    </label>
                    <div class="input-group">
                        <input id="cron_url" class="form-control"  value="curl -s {{route('cron.run')}}" >
                        <span class="input-group-text cursor-pointer" onclick="copyUrl()">
                            {{translate('Copy URL')}}
                        </span>
                    </div>
                </div>
                <div class="col-6">
                    <label for="queue_url" class="form-label">
                        {{translate('Queue URL')}}
                    </label>
                    <div class="input-group">
                        <input id="queue_url" class="form-control"  value="curl -s {{route('queue.work')}}" >
                        <span class="input-group-text cursor-pointer" onclick="copyQueueUrl()">
                            {{translate('Copy URL')}}
                        </span>
                    </div>
                </div>

                

            </div>

            <div class="text-start">
                <button type="submit"
                    class="btn btn-success waves ripple-light"
                    id="add-btn">
                    {{translate('Submit')}}
                </button>
            </div>

        </form>
    </div>

</div>

