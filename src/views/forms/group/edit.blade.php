<p>{{count($group->users)}} user(s) in this group.</p>
@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::model($group, array('url' => array('um/group', $group->id), 'method'=>'put')) !!}
@include('um::partial_forms.group')
{!! Form::submit('Save', ["class"=>"btn btn-default btn-sm"]) !!}
<a href="{{url('um/group')}}" class="btn btn-default btn-sm">Cancel</a>
{!! Form::close() !!}