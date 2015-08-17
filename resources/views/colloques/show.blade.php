@extends('layouts.colloque')
@section('content')

<div class="row">
    <div class="col-md-12">
        <h2>Inscription</h2><br/>

        <div class="media">
            <div class="media-left">
                <img width="170px" style="margin-right: 20px;" class="media-object img-thumbnail" src="{{ asset($colloque->illustration) }}" alt="{{ $colloque->titre }}">
            </div>
            <div class="media-body">
                <h4 class="media-heading">{{ $colloque->titre }}<br/>{{ $colloque->soustitre }}</h4>
                <p><strong>{{ $colloque->event_date }}</strong> </p>
                {{-- <hr/>
                <p><strong>Lieu:</strong> {{ $colloque->location->name }}, {{ $colloque->location->adresse }}</p>
                <p><strong>Délai d'inscription:</strong> {{ $colloque->registration_at->formatLocalized('%d %B %Y') }}</p>--}}
                <div class="row">
                    <div class="col-md-8">

                        <!-- Simple Inscription -->
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <form role="form" class="validate-form" method="POST" action="registration" data-validate="parsley" >
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <fieldset>

                                        @if(!$colloque->prices->isEmpty())
                                            @include('colloques.partials.prices', ['select' => 'price_id'])
                                        @endif

                                        <h4>Merci de préciser</h4>
                                        @if(!$colloque->options->isEmpty())
                                            @include('colloques.partials.options', ['select' => 'groupes'])
                                        @endif

                                        <input name="user_id" value="{{ Auth::user()->id }}" type="hidden">
                                        <input name="colloque_id" value="{{ $colloque->id }}" type="hidden">

                                        <button class="btn btn-danger pull-right" type="submit">Envoyer</button>
                                    </fieldset>
                                </form>
                             </div>
                        </div><!-- end panel -->
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">

                        <!-- Multiple inscription -->

                        <div class="panel panel-default">
                            <div class="panel-body">

                                <form role="form" class="validate-form" method="POST" action=" registration" data-validate="parsley" >
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                    <h4>Inscriptions multiple</h4>

                                    <h4><a href="#" id="cloneBtn"><i class="glyphicon glyphicon-plus-sign"></i></a></h4>

                                    <div id="wrapper_clone">
                                        <fieldset class="field_clone" id="fieldset_clone">
                                            <div class="form-group">
                                                <label>Nom du participant</label>
                                                <input name="participant[]" required class="form-control" value="" type="text">
                                            </div>

                                            @if(!$colloque->prices->isEmpty())
                                                @include('colloques.partials.prices', ['select' => 'price_id[]'])
                                            @endif

                                            @if(!$colloque->options->isEmpty())
                                                @include('colloques.partials.options', ['select' => 'groupes[]'])
                                            @endif
                                        </fieldset>
                                    </div>

                                    <input name="user_id" value="{{ Auth::user()->id }}" type="hidden">
                                    <input name="colloque_id" value="{{ $colloque->id }}" type="hidden">
                                    <div class="clearfix"></div><br/>
                                    <button class="btn btn-danger" type="submit">Envoyer</button>
                                </form>
                            </div>
                        </div><!-- end panel -->


                    </div>
                </div>

            </div>
        </div>


    </div>
</div>

@stop