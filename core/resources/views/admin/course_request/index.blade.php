@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card table-dropdown-solved b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--lg table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('ID')</th>
                                    <th>@lang('Course title')</th>
                                    <th>@lang('User name')</th>
                                    <th>@lang('E-mail')</th>

                                    <th>@lang('created at')</th>

                                    {{-- <th>@lang('Min Spend')</th>
                                    <th>@lang('Max Spend')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Discount')</th>
                                    <th>@lang('Status')</th> --}}
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($CoursePurchases as $CoursePurchase)
                                    <tr id="coursePurchaseRow_{{ $CoursePurchase->id }}">
                                        <td>{{ __($CoursePurchase->id) }}</td>
                                        <td>{{ __($CoursePurchase->course->title) }}</td>
                                        <td>{{ __($CoursePurchase->user->fullname) }}</td>
                                        <td>{{ __($CoursePurchase->user->email) }}</td>
                                        <td>{{ $CoursePurchase->created_at->format('Y-m-d H:i:s') }}</td>


                                        {{-- <td>{{ $general->cur_sym }}{{ showAmount($CoursePurchase->minimum_spend) }}</td>
                                        <td>{{ $general->cur_sym }}{{ showAmount($CoursePurchase->maximum_spend) }}</td>
                                        <td>{{ $CoursePurchase->code }}</td> --}}
                                        {{-- <td>{{ $general->cur_sym }}{{ showAmount($CoursePurchase->discount_amount) }} {{ $CoursePurchase->discount_type ? '%' : ' ' . __($general->cur_text) }}</td> --}}
                                        {{-- <td> @php echo $CoursePurchase->statusBadge; @endphp </td> --}}
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary approveBtn" data-id="{{ $CoursePurchase->id }}" >
                                                    <i class="fas fa-vote-yea"></i>@lang('Approve')
                                                </button>
                                                {{-- @if ($CoursePurchase->status == Status::DISABLE) --}}
                                                    <button type="button"  class="btn btn-sm btn-outline--danger rejectBtn" data-id="{{ $CoursePurchase->id }}">
                                                        <i class="fas fa-ban"></i> @lang('Reject')
                                                    </button>
                                                {{-- @else
                                                    <button class="btn btn-sm btn-outline--danger rejectBtn" data-question="@lang('Are you sure to disable this CoursePurchase?')" data-action="{{ route('admin.CoursePurchase.status', $CoursePurchase->id) }}">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif --}}

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($CoursePurchases->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($CoursePurchases) }}
                    </div>
                @endif
            </div>
        </div>
    </div>


  

    <x-confirmation-modal />
@endsection


@push('script')
    <script>
        (function($) {
    "use strict";

    let modal = $('#CoursePurchaseModal');

    modal.on('hidden.bs.modal', function() {
        modal.find('form')[0].reset();
    });

    $('[name=discount_type]').on('change', function() {
        let discountType = $(this).val() * 1;
        $('.discountType').text(discountType ? '%' : `{{ __($general->cur_text) }}`);
    }).change();

// Event listener for the click event on the approve button
$('.approveBtn').on('click', function() {
    console.log($(this).data('id'));

    let action = "{{ route('admin.CoursePurchase.status')}}";
    let url = $(this).data('action');

    fetch(action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            id: $(this).data('id'),
            st: 1, // 1 for approve, 2 for reject
        }),
    })
    .then(response => response.json())
        .then(data => {
            // Notification for success
            // Replace this with your own implementation of notification
            notify('success', 'Course purchase approved Successfully');
            $('#coursePurchaseRow_' + $(this).data('id')).remove();

        })
        .catch(error => {
            // Notification for error
            // Replace this with your own implementation of notification
            notify('error', 'Course purchase approval failed' );

        });
});

// Event listener for the click event on the reject button
$('.rejectBtn').on('click', function() {
    let action = "{{ route('admin.CoursePurchase.status')}}";
    let url = $(this).data('action');
    console.log($(this).data('id'));

    fetch(action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            id: $(this).data('id'),
            st: 2, // 1 for approve, 2 for reject
        }),
    })
    .then(response => response.json())
        .then(data => {
            // Notification for success
            // Replace this with your own implementation of notification
            notify('warning', 'Course purchase rejected Successfully');
            $('#coursePurchaseRow_' + $(this).data('id')).remove();

        })
        .catch(error => {
            // Notification for error
            // Replace this with your own implementation of notification
            notify('error', 'Course purchase rejection failed');
        });
});


})(jQuery);

    </script>
@endpush
