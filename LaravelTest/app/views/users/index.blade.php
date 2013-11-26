@extends('layouts.scaffold')

@section('main')

<h1>All Users</h1>

<p>{{ link_to_route('users.create', 'Add new user') }}</p>

@if ($users->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>StaffCode</th>
				<th>Password</th>
				<th>Name</th>
				<th>Gender</th>
				<th>Ip</th>
				<th>Email</th>
				<th>Info</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($users as $user)
				<tr>
					<td>{{{ $user->staffCode }}}</td>
					<td>{{{ $user->password }}}</td>
					<td>{{{ $user->name }}}</td>
					<td>{{{ $user->gender }}}</td>
					<td>{{{ $user->ip }}}</td>
					<td>{{{ $user->email }}}</td>
					<td>{{{ $user->info }}}</td>
                    <td>{{ link_to_route('users.edit', 'Edit', array($user->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('users.destroy', $user->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no users
@endif

@stop
