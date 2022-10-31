@extends('layouts.app')
@section('content')
<div class="description comment">
    <div class="breadcrumb">
        <a href="{{ url()->previous() }}">{{ trans_choice('ticket.ticket', 2) }}</a>
    </div>
</div>

{{ Form::open(["url" => route("tickets.store")]) }}
<div class="comment description actions">
    <table class="maxw600 no-padding">
        <tr>
            <td class="w20"><b> {{ __('ticket.requester') }}:</b></td>
        </tr>
        <tr>
            <td>{{ __('user.name')  }}: </td>
            <td class="w100"><input type="name" name="requester[name]" class="w100" required></td>
        </tr>
        <tr>
            <td>{{ __('user.email') }}: </td>
            <td class="w100"><input type="email" name="requester[email]" class="w100" required></td>
        </tr>
    </table>
</div>

<div class="">

    <!-- NUEVO -->
    <table>
        <tr>
            <td>
                <table class="">
                    <tr>
                        <td class="w20">{{ __('ticket.subject') }}: </td>
                        <td><input name="title" class="w100" required /></td>
                    </tr>
                    <tr>
                        <td>{{ trans_choice('ticket.tag', 2)}}: </td>
                        <td><input name="tags" id="tags" /></td>
                    </tr>
                    <tr>
                        <td>{{ __('ticket.comment')}}: </td>
                        <td><textarea name="body" required></textarea></td>
                    </tr>
                    @include('components.assignTeamField')
                    <tr>
                        <td>{{ __('ticket.status') }}: </td>
                        <td>
                            {{ Form::select("status", [
                                App\Ticket::STATUS_NEW      => __("ticket.new"),
                                App\Ticket::STATUS_OPEN     => __("ticket.open"),
                                App\Ticket::STATUS_PENDING  => __("ticket.pending"),
                            ]) }}
                            <button class="uppercase ph3 ml1"> @icon(comment) {{ __('ticket.new') }}</button>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="">
                    <tr>
                        <td class="w30">NRO Invoice</td>
                        <td><input name="invoice_number" class="w80" required /></td>
                    </tr>
                    <tr>
                        <td class="w30">UUID</td>
                        <td><input name="uuid" class="w80" required /></td>
                    </tr>
                    <tr>
                        <td class="w30">NRO Load</td>
                        <td><input name="load_number" class="w80" required /></td>
                    </tr>
                    <tr>
                        <td class="w30">NRO Order</td>
                        <td><input name="order_number" class="w80" required /></td>
                    </tr>
                    <tr>
                        <td class="w30">Cust Ref</td>
                        <td><input name="cust_ref" class="w80" required /></td>
                    </tr>
                    <tr>
                        <td class="w30">Total</td>
                        <td><input name="total" class="w80" required /></td>
                    </tr>
                    <tr>
                        <td class="w30">Moneda / Coin</td>
                        <td><input name="coin_type" class="w80" required /></td>
                    </tr>
                    <tr>
                        <td class="w20">Error Type / Tipo error</td>
                        <td><input name="error_type" class="w80" required /></td>
                    </tr>
                    <!--  -->
                </table>
            </td>
        </tr>
    </table>

    {{ Form::close() }}
</div>
@endsection


@section('scripts')
@include('components.js.taggableInput', ["el" => "tags", "endpoint" => "tickets", "object" => null])
@endsection