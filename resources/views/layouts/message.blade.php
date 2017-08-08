@if ($message = Session::get('success'))
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">
            <i class="glyphicon glyphicon-remove"></i>
        </button>
        <strong>{{ $message }}</strong>
    </div>
@endif

@if ($messageError = Session::get('errorsMessage'))
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">
            <i class="fa fa-times"></i>
        </button>
        <strong>{{ $messageError }}</strong>
    </div>
@endif

@if ($message = Session::get('addNewSuccess'))
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">
            <i class="fa fa-times"></i>
        </button>
        <strong>Add new success!</strong>
    </div>
@endif

@if ($message = Session::get('addNewError'))
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">
            <i class="fa fa-times"></i>
        </button>
        <strong>Add new error!</strong>
    </div>
@endif

@if ($message = Session::get('AssignUserNotFoundError'))
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">
            <i class="fa fa-times"></i>
        </button>
        <strong>This email not found in system!</strong>
    </div>
@endif

@if ($message = Session::get('UpdateAssignUserSuccess'))
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">
            <i class="fa fa-times"></i>
        </button>
        <strong>Update Success!</strong>
    </div>
@endif

@if ($message = Session::get('DeleteAssignedUserSuccess'))
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">
            <i class="fa fa-times"></i>
        </button>
        <strong>{{ $message }}</strong>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <button type="button" class="close closeMessage">
            <i class="fa fa-times"></i>
        </button>
        <span id="message">{{$errors->first()}}</span>
    </div>
@endif