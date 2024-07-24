@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="dashboard-body">
        <div class="card p-0">
            @if ($supports->count())
                <div class="card-body p-0">
                    <div class="table-responsive--lg table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Last Reply')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supports as $support)
                                    <tr>
                                        <td> <a href="{{ route('ticket.view', $support->ticket) }}" class="fw-bold"> [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }} </a></td>
                                        <td>
                                            @php echo $support->statusBadge; @endphp
                                        </td>
                                        <td>
                                            @php echo $support->priorityBadge; @endphp
                                        </td>
                                        <td>{{ diffForHumans($support->last_reply) }} </td>
                                        <td>
                                            <a href="{{ route('ticket.view', $support->ticket) }}" class="btn btn--base btn--sm"> <i class="las la-desktop"></i> @lang('Details')</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <span class="empty-slip-message">
                    <span class="d-flex justify-content-center align-items-center">
                        <img src="{{ asset($activeTemplateTrue . '/images/empty_list.png') }}" alt="image">
                    </span>
                    {{ __($emptyMessage) }}
                </span>
            @endif
            @if ($supports->hasPages())
                <div class="card-footer">
                    {{ $supports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
@push('breadcrumb-buttons')
    <div class="button-list">
        <li class="button-list__item">
            <a href="{{ route('ticket.open') }}" class="btn btn--base flex-align btn--sm"> <span class="icon"><i class="las la-plus-circle"></i></span>@lang('Open new')</a>
        </li>
    </div>
@endpush
