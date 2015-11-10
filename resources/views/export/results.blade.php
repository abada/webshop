@extends('backend.layouts.master')
@section('content')

    <div class="row">
        <div class="col-md-12">

            <h3>Résultats</h3>

            <div class="options text-left" style="margin-bottom: 10px;">
                <div class="btn-toolbar">
                    <a href="{{ url('admin/export/user') }}" class="btn btn-default"><i class="fa fa-arrow-left"></i> &nbsp;Retour</a>
                </div>
            </div>

            <div class="row">

                <div class="col-md-12">

                    <div class="panel panel-midnightblue">
                        <div class="panel-body">
                            <h4><i class="fa fa-home"></i> &nbsp;Résultats adresses</h4>
                            <blockquote>
                                @foreach($terms as $label => $terms)
                                    <p>
                                        <strong>{{ $label }}:</strong>
                                        @foreach($terms as $term)
                                           {{ $term }}
                                        @endforeach
                                    </p>
                                @endforeach
                                <p><strong>Trouvés :</strong> {{ $adresses->count() }}</p>
                            </blockquote>
                            @if(isset($adresses) && !$adresses->isEmpty())
                                <table class="table" style="margin-bottom: 0px;" >
                                    <thead>
                                    <tr>
                                        <th class="col-sm-1">Action</th>
                                        <th class="col-sm-3">Nom</th>
                                        <th class="col-sm-3">Email</th>
                                        <th class="col-sm-2">Entreprise</th>
                                        <th class="col-sm-2">Ville</th>
                                    </tr>
                                    </thead>
                                    <tbody class="selects">
                                        @foreach($adresses as $adresse)
                                            <tr>
                                                <?php $url = ($adresse->user_id > 0 ? 'user/'.$adresse->user_id : 'adresse/'.$adresse->id); ?>
                                                <td><a class="btn btn-sky btn-sm" href="{{ url('admin/'.$url) }}">&Eacute;diter</a></td>
                                                <td><strong>{{ $adresse->name }}</strong></td>
                                                <td>{{ $adresse->email }}</td>
                                                <td>{{ $adresse->company }}</td>
                                                <td>{{ $adresse->ville }}</td>
                                                <td class="text-right">
                                                    {!! Form::open(array('route' => array('admin.adresse.destroy', $adresse->id), 'method' => 'delete')) !!}
                                                    <button data-action="{{ $adresse->name }}" class="btn btn-danger btn-sm deleteAction">Supprimer</button>
                                                    {!! Form::close() !!}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

@stop