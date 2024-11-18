@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Obtener Métricas</h1>

    <form id="metricForm">
        @csrf

        <!-- Input Text para URL -->
        <div class="mb-3">
            <label for="url" class="form-label">URL</label>
            <input type="text" class="form-control" id="url" name="url" placeholder="https://example.com">
        </div>

        <!-- Grupo de Checkboxes para Categorías -->
        <div class="mb-3">
            <label class="form-label">Categorías</label>
            <div>
                @foreach ($categories as $category)
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="category-{{ $category->id }}" name="categories[]" value="{{ $category->name }}">
                        <label class="form-check-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Select para Estrategias -->
        <div class="mb-3">
            <label for="strategy" class="form-label">Estrategia</label>
            <select class="form-select" id="strategy" name="strategy">
                <option value="" selected>Seleccione una estrategia</option>
                @foreach ($strategies as $strategy)
                    <option value="{{ $strategy->name }}">{{ $strategy->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Botón de Submit -->
        <button type="button" id="submitMetrics" class="btn btn-primary">Obtener Métricas</button>
    </form>

    <!-- Resultados -->
    <div id="results" class="mt-4"></div>
    <button id="saveMetrics" class="btn btn-success mt-3" style="display: none;">Save Metric Run</button>

</div>

<script>
    let fetchedMetrics = null;

    document.getElementById('submitMetrics').addEventListener('click', function () {
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
            const resultsDiv = document.getElementById('results');
            const saveButton = document.getElementById('saveMetrics');
            const dataForm   = formData.getAll("categories[]");

            resultsDiv.innerHTML = '';
            saveButton.style.display = 'none';

            if (data.success) {
                fetchedMetrics = {
                    url: formData.get('url'),
                    metrics: {
                        accessibility_metric: dataForm.indexOf("ACCESSIBILITY") == -1 ? "" : data.data.lighthouseResult.categories.accessibility.score * 100,
                        pwa_metric:             dataForm.indexOf("PWA") == -1 ? "" : data.data.lighthouseResult.categories.pwa.score * 100,
                        performance_metric: dataForm.indexOf("PERFORMANCE") == -1 ? "" : data.data.lighthouseResult.categories.performance.score * 100,
                        seo_metric: dataForm.indexOf("SEO") == -1 ? "" : data.data.lighthouseResult.categories.seo.score * 100,
                        best_practices_metric: dataForm.indexOf("BEST_PRACTICES") == -1 ? "" : data.data.lighthouseResult.categories['best-practices'].score * 100,
                    },
                    strategy: formData.get('strategy'),
                };

                resultsDiv.innerHTML = `<pre>${JSON.stringify(fetchedMetrics.metrics, null, 2)}</pre>`;
                saveButton.style.display = 'block';
            } else {
                resultsDiv.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    document.getElementById('saveMetrics').addEventListener('click', function () {
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
                alert(data.message);
            } else {
                alert('Error al guardar las métricas: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
</script>
@endsection
