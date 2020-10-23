@extends("sharp::layout")

@section("content")

    <div id="sharp-app">
        @include("sharp::partials._menu")
<div class="SharpActionView">
    <div class="SharpDashboardPage">
        <div class="container">
            <div class="SharpEntityList">
                <div class="SharpDataList__table SharpDataList__table--border">
                    <div class="SharpDataList__thead">
                        <div class="SharpDataList__row container SharpDataList__row--header">
                            <div class="SharpDataList__cols">
                                <div class="row mx-n2 mx-md-n3">
                                    <div class="px-2 px-md-3 SharpDataList__th col-1 col-md-1"><span>Level</span></div>
                                    <div class="px-2 px-md-3 SharpDataList__th col-1 col-md-1"><span>Level Name</span></div>
                                    <div class="px-2 px-md-3 SharpDataList__th col-1 col-md-5"><span>Message</span></div>
                                    <div class="px-2 px-md-3 SharpDataList__th col-1 col-md-1"><span>Context</span></div>
                                    <div class="px-2 px-md-3 SharpDataList__th col-1 col-md-1"><span>Channel</span></div>
                                    <div class="px-2 px-md-3 SharpDataList__th col-1 col-md-2"><span>Date</span></div>
                                    <div class="px-2 px-md-3 SharpDataList__th col-1 col-md-1"><span>Extra</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="SharpDataList__tbody">
                        <div>
                            @foreach($logItems as $item)
                                <div class="SharpDataList__row container">
                                    <div class="SharpDataList__cols">
                                        <div class="row mx-n2 mx-md-n3">
                                            <div class="px-2 px-md-3 SharpDataList__td col-1 col-md-1">
                                                <div class="SharpDataList__td-html-container">{{$item->level}}</div>
                                            </div>
                                            <div class="px-2 px-md-3 SharpDataList__td col-1 col-md-1">
                                                <div class="SharpDataList__td-html-container">{{$item->level_name}}</div>
                                            </div>
                                            <div class="px-2 px-md-3 SharpDataList__td col-1 col-md-5">
                                                <div class="SharpDataList__td-html-container">{{$item->message}}</div>
                                            </div>
                                            <div class="px-2 px-md-3 SharpDataList__td col-1 col-md-1">
                                                <div class="SharpDataList__td-html-container">{{json_encode($item->context)}}</div>
                                            </div>
                                            <div class="px-2 px-md-3 SharpDataList__td col-1 col-md-1">
                                                <div class="SharpDataList__td-html-container">{{$item->channel}}</div>
                                            </div>
                                            <div class="px-2 px-md-3 SharpDataList__td col-1 col-md-2">
                                                <div class="SharpDataList__td-html-container">{{(new \DateTime($item->datetime))->format('Y-m-d H:i:s')}}</div>
                                            </div>
                                            <div class="px-2 px-md-3 SharpDataList__td col-1 col-md-1">
                                                <div class="SharpDataList__td-html-container">{{json_encode($item->extra)}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


</div>

@endsection
