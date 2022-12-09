<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">Change Password for User: <b>{$user->first_name} {$user->last_name} ({$user->email})</b><a class="pull-right" href="{$moduleLink}&page=dashboard"><i class="glyphicon glyphicon-home"></i></a></div>
            <div class="panel-body">

                {if !empty($alert) && $alert.type == 'error'}
                    <div class="row">
                        <div class="col-md-12">
                        <div class="alert alert-danger" role="alert">{{$alert.message}}</div>
                        </div>
                    </div>
                {/if}
                <form action="{$moduleLink}&page=change&id={$user->id}" method="post">
                    <div class="form-group">
                        <label for="newPw" class="control-label">New Password</label>
                        <div class="input-group">
                            <input type="text" name="newPw" id="newPw" class="form-control" />
                            <span class="input-group-addon"><a href="#" id="genPW" onClick="event.preventDefault(); generatePassword()"><i class="glyphicon glyphicon-repeat"></i></a></span>
                        </div>
                    </div>
                    <input type="hidden" name="token" value="{$csrfToken}">
                    <input type="submit" value="Change Password" class="btn btn-primary pull-right" />
                </form>
            </div>
        </div>
    </div>
</div>

{$generatorScript|unescape: "html" nofilter}