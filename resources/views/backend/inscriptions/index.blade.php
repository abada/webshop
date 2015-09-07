@extends('backend.layouts.master')
@section('content')
<?php $helper = new \App\Droit\Helper\Helper(); ?>
<div class="row">
    <div class="col-md-12">

        <div class="options text-right" style="margin-bottom: 10px;">
            <div class="btn-toolbar">
               <a href="{{ url('admin/inscription/create') }}" class="btn btn-success"><i class="fa fa-plus"></i> &nbsp;Ajouter</a>
            </div>
        </div>


        <div class="panel panel-midnightblue">
            <div class="panel-heading">
                <h4><i class="fa fa-tasks"></i> &nbsp;Inscriptions</h4>
            </div>

            <div class="panel-body">
                <div class="table-responsive">

                    <table class="table" style="margin-bottom: 0px;" id="generic">
                        <thead>
                        <tr>
                            <th class="col-sm-1">Action</th>
                            <th class="col-sm-2">Nom</th>
                            <th class="col-sm-2">Email</th>
                            <th class="col-sm-2">Participant</th>
                            <th class="col-sm-2">No</th>
                            <th class="col-sm-2">Date</th>
                            <th class="col-sm-1"></th>
                        </tr>
                        </thead>
                        <tbody class="selects">

                            @if(!empty($inscriptions))
                                @foreach($inscriptions as $inscription)

                                    @if(is_array($inscription))
                                        @foreach($inscription as $register)
                                            @include('backend.inscriptions.partials.row', ['inscription' => $register, 'group' => true])
                                        @endforeach
                                    @else
                                        @include('backend.inscriptions.partials.row', ['inscription' => $inscription, 'group' => false])
                                    @endif

                                @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@stop