@props(['resep'])

<div class="resep">

    <div class="resep-content">

        <div class="resep-logo">
            <span class="material-icons-round">
                lunch_dining
            </span>
        </div>

        <div class="resep-detail">

            <h1>
                {{ $resep->title }}
            </h1>

            <div class="resep-content-detail">

                <div>
                    <span class="material-icons-round">
                        watch_later
                    </span>

                    <p>
                        {{ $resep->cook_duration }}
                    </p>
                </div>

                <div>
                    <span class="material-icons-round">
                        menu_book
                    </span>

                    <p>
                        Bahan Tersedia
                    </p>
                </div>

            </div>

            <div class="resep-verified">

                <p>
                    {{ $resep->user->name ?? 'Unknown' }}
                </p>

            </div>

        </div>

    </div>

    <span class="material-icons-round">
        arrow_forward_ios
    </span>

</div>