@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <ul class="nav nav-tabs justify-content-center" role="tablist">
        <li class="nav-item" role="presentation">
            <a href="#run-metrics" class="nav-link active" data-bs-toggle="tab" role="tab" aria-selected="true">Obtener Métricas</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="#metric-history" class="nav-link" data-bs-toggle="tab" role="tab" aria-selected="false">Historial de Métricas</a>
        </li>
    </ul>
    <div class="tab-content mt-3">

        <div class="tab-pane active" id="run-metrics" role="tabpanel" aria-labelledby="run-metrics-tab">
            <h1 class="text-center mb-4">Ejecutar Métricas</h1>

            <form id="metricForm" class="rounded shadow p-4">
                @csrf

                <div class="mb-3">
                    <label for="url" class="form-label">URL</label>
                    <input type="text" class="form-control" id="url" name="url" placeholder="https://example.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categorías</label>
                    <div class="d-flex flex-wrap">
                        @foreach ($categories as $category)
                            <div class="form-check me-3 mb-2">
                                <input type="checkbox" class="form-check-input" id="category-{{ $category->id }}" name="categories[]" value="{{ $category->name }}">
                                <label class="form-check-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label for="strategy" class="form-label">Estrategia</label>
                    <select class="form-select" id="strategy" name="strategy" required>
                        <option value="" selected>Seleccione una estrategia</option>
                        @foreach ($strategies as $strategy)
                            <option value="{{ $strategy->id }}">{{ $strategy->name }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <button type="button" id="submitMetrics" class="btn btn-primary btn-lg rounded-pill">Iniciar Análisis</button>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" id="saveMetrics" class="btn btn-success btn-lg rounded-pill" style="display: none;">Guardar Resultados</button>
                        </div>
                    </div>
                </div>

                <div id="results" class="mt-4 d-flex flex-wrap justify-content-center"></div>
            </div>
            </form>

           
            <div class="tab-pane" id="metric-history" role="tabpanel" aria-labelledby="metric-history-tab">
                <h1 class="text-center mb-4">Historial de Métricas</h1>

                <div id="metric-history-table-container">
                    <table id="metricHistoryTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>URL</th>
                                <th>Accesibilidad</th>
                                <th>PWA</th>
                                <th>Performance</th>
                                <th>SEO</th>
                                <th>Mejores Prácticas</th>
                                <th>Estrategia</th>
                                <th>Fecha de Creación</th>
                            </tr>
                        </thead>
                        <tbody id="metricHistoryTableBody">
                        </tbody>
                    </table>
                </div>

                <div id="pagination-container"></div>
            </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    let fetchedMetrics = null;

    const metricHistoryTableBody = document.getElementById('metricHistoryTableBody');
    const metricHistoryLoadingText = document.getElementById('metricHistoryLoadingText');
    const metricHistorySpinner = document.querySelector('.spinner-border');
    const metricHistoryLoadMore = document.querySelector('.load-more');

    const resultsDiv = document.getElementById('results');
    const saveButton = document.getElementById('saveMetrics');
    const submitMetricsButton = document.getElementById('submitMetrics');

    if (metricHistoryLoadingText) {
        metricHistoryLoadingText.style.display = 'block';
        metricHistorySpinner.style.display = 'inline-block';
    }


    // Función para obtener métricas
    submitMetricsButton.addEventListener('click', function () {
        const form = document.getElementById('metricForm');
        const formData = new FormData(form);

        fetch('{{ route("metric-history-run.fetch-metrics") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            resultsDiv.innerHTML = '';
            saveButton.style.display = 'none';

            if (data.success) {
                let categories = data.data.lighthouseResult.categories;

                fetchedMetrics = {
                    url: formData.get('url'),
                    strategy_id: formData.get('strategy'),
                    metrics: {
                        accessibility_metric: categories.accessibility ? categories.accessibility.score : 0,
                        pwa_metric: 0,
                        performance_metric:  categories.performance ? categories.performance.score : 0,
                        seo_metric: categories.seo ? categories.seo.score : 0,
                        best_practices_metric: categories['best-practices'] ? categories['best-practices'].score : 0,
                },
                };

                // Construye las tarjetas de métricas
                let metricCards = '';
                for (const metricName in fetchedMetrics.metrics) {
                    metricCards += `
                        <div class="card mx-2 mb-4 bg-light text-dark" style="width: 12rem;">
                            <div class="card-body text-center">
                                <h5 class="card-title">${metricName.replace('_metric', '').toUpperCase()}</h5>
                                <p class="card-text fs-2">${fetchedMetrics.metrics[metricName]}</p>
                            </div>
                        </div>
                    `;
                }

                resultsDiv.innerHTML = metricCards;
                saveButton.style.display = 'block';
            } else {
                resultsDiv.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }
        })
        .catch(error => {
            console.error('Error al obtener las métricas:', error);
            resultsDiv.innerHTML = `<div class="alert alert-danger">Error al procesar la solicitud.</div>`;
        });
    });

    // Función para guardar métricas
    saveButton.addEventListener('click', function () {
        if (!fetchedMetrics) return;

        fetch('{{ route("metric-history-run.save-metrics") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(fetchedMetrics)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let button = document.querySelector('.load-more');

                alert(data.message);

                fetchedMetrics = null;
                saveButton.style.display = 'none';
                resultsDiv.innerHTML = '';

                loadMoreMetrics(1);
            } else {
                alert('Error al guardar las métricas: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error al guardar las métricas:', error);
            alert('Error al guardar las métricas.');
        });
    });

    document.addEventListener('click', (event) => {
        if (event.target.classList.contains('load-more')) {
            // Asegurarse de que el botón tenga el atributo data-page
            const button = event.target;
            if (!button.dataset.page) {
                button.dataset.page = 1;
            }

            const currentPage = parseInt(button.dataset.page);
            const nextPage = currentPage + 1;
            button.dataset.page = nextPage;

            loadMoreMetrics(nextPage);
        }
    });

    function loadMoreMetrics(page) {
        $.ajax({
            url: '{{ route("metric-history-run.index") }}?page=' + page,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    let tableRows = '';
                    response.data.data.forEach(metric => {
                        tableRows += `
                            <tr>
                                <td>${metric.id}</td>
                                <td>${metric.url}</td>
                                <td>${metric.accessibility_metric}</td>
                                <td>${metric.pwa_metric}</td>
                                <td>${metric.performance_metric}</td>
                                <td>${metric.seo_metric}</td>
                                <td>${metric.best_practices_metric}</td>
                                <td>${metric.strategy.name}</td>
                                <td>${new Date(metric.created_at).toLocaleDateString()}</td>
                            </tr>
                        `;
                    });

                    if(page == 1)   $('#metricHistoryTableBody').empty().append(tableRows);
                    else            $('#metricHistoryTableBody').append(tableRows);

                    // Agrega los enlaces de paginación si hay más páginas
                    if (response.data.next_page_url) {
                        if( !$(".load-more").is(":visible")) $('#pagination-container').append('<button class="btn btn-primary load-more" data-page="1">Cargar más</button>');
                    }
                } else {
                    console.error('Error al cargar más métricas:', response.error);
                }
            },
            error: function(error) {
                console.error('Error al cargar más métricas:', error);
            }
        });
    }

    // Inicialmente, carga la primera página de datos
    loadMoreMetrics(1);

});
</script>

@endsection