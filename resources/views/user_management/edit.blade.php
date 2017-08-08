<div class="panel panel-default">
  <div class="panel-body">
    <form class="form-horizontal container-web" method="post" action="{{Route('user-management.postEdit')}}">
        {{csrf_field()}}
          <div class="form-group">
            <label class="col-lg-3 control-label">Is Admin</label>
            <div class="col-lg-9">
                <label class="label-checkbox">
                    <input type="checkbox" style="opacity: 0;" class="role" name="roles_admin" {{ $user->inRole(1) ? 'checked' : ''}} value="1">
                    <span class="custom-checkbox"></span>
                    Admin
                </label>
            </div><!-- /.col -->
            <input type="hidden" name="user_id" value="{{$user_id}}">
        </div>
      <div class="form-group">
        <div class="col-sm-7 col-sm-offset-3">
          <button class="btn btn-success"  id="save" type="submit">Save</button>
        </div>
      </div>
    </form>
  </div><!-- /panel-body -->
</div>