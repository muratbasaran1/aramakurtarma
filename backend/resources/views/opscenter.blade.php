<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>OpsCenter Paneli</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <style>
            :root {
                color-scheme: light dark;
                font-family: 'Figtree', sans-serif;
                background-color: #f4f4f5;
                color: #18181b;
            }

            body {
                margin: 0;
                padding: 2rem;
                min-height: 100vh;
            }

            a {
                color: inherit;
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                display: flex;
                flex-direction: column;
                gap: 2rem;
            }

            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .stats {
                display: grid;
                gap: 1rem;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            }

            .card {
                background: white;
                border-radius: 1rem;
                padding: 1.5rem;
                box-shadow: 0 10px 25px -15px rgba(15, 23, 42, 0.5);
            }

            .card h3 {
                margin: 0;
                font-size: 1rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #6b7280;
            }

            .card strong {
                display: block;
                font-size: 2rem;
                margin-top: 0.5rem;
                color: #0f172a;
            }

            .section-title {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 0.75rem;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                background: white;
                border-radius: 1rem;
                overflow: hidden;
                box-shadow: 0 10px 25px -15px rgba(15, 23, 42, 0.5);
            }

            th, td {
                padding: 0.75rem 1rem;
                text-align: left;
                border-bottom: 1px solid #e5e7eb;
            }

            th {
                background: #f3f4f6;
                font-size: 0.85rem;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #4b5563;
            }

            tbody tr:last-child td {
                border-bottom: none;
            }

            .empty-state {
                padding: 1rem;
                text-align: center;
                color: #6b7280;
            }

            .tenant-select select {
                padding: 0.65rem 0.9rem;
                font-size: 1rem;
                border-radius: 0.75rem;
                border: 1px solid #cbd5f5;
                background: white;
                min-width: 220px;
            }

            .badge {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.6rem;
                border-radius: 999px;
                font-size: 0.75rem;
                font-weight: 600;
                letter-spacing: 0.04em;
            }

            .badge.status-open { background: #dbeafe; color: #1d4ed8; }
            .badge.status-active { background: #dcfce7; color: #15803d; }
            .badge.status-closed { background: #fee2e2; color: #b91c1c; }
            .badge.status-assigned { background: #ede9fe; color: #6d28d9; }
            .badge.status-in_progress { background: #fef3c7; color: #b45309; }
            .badge.status-done { background: #bbf7d0; color: #166534; }
            .badge.status-verified { background: #bae6fd; color: #0369a1; }
            .badge.status-active-inventory { background: #dcfce7; color: #15803d; }
            .badge.status-service { background: #fef08a; color: #92400e; }
            .badge.status-retired { background: #fee2e2; color: #b91c1c; }

            footer {
                text-align: center;
                color: #6b7280;
                font-size: 0.85rem;
                padding-bottom: 2rem;
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    background-color: #0f172a;
                    color: #e2e8f0;
                }

                .card, table {
                    background: #1e293b;
                    color: inherit;
                }

                th {
                    background: #111827;
                    color: #94a3b8;
                }

                td {
                    border-bottom-color: #1f2937;
                }

                .tenant-select select {
                    background: #0f172a;
                    color: inherit;
                    border-color: #334155;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <header class="header">
                <div>
                    <h1 style="margin: 0; font-size: 2rem; font-weight: 600;">OpsCenter Paneli</h1>
                    <p style="margin: 0.4rem 0 0; color: #4b5563;">Tenant bazlı olay, görev ve envanter görünümü</p>
                </div>
                <form class="tenant-select" method="get">
                    <label for="tenant" style="display:block; font-size:0.9rem; font-weight:500; color:#4b5563; margin-bottom:0.4rem;">Tenant Seç</label>
                    <select id="tenant" name="tenant" onchange="this.form.submit()">
                        @foreach ($tenants as $tenantOption)
                            <option value="{{ $tenantOption->slug }}" @selected(optional($tenant)->slug === $tenantOption->slug)>
                                {{ $tenantOption->name }} ({{ $tenantOption->slug }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </header>

            @if ($tenant === null)
                <div class="card">
                    <p class="empty-state">Kayıtlı tenant bulunamadı. Lütfen seed komutu çalıştırdıktan sonra paneli yenileyin.</p>
                </div>
            @else
                <section>
                    <h2 class="section-title">Durum Özeti — {{ $tenant->name }}</h2>
                    <div class="stats">
                        <div class="card">
                            <h3>Aktif Olaylar</h3>
                            <strong>{{ $summary['incidentCounts']['active'] ?? 0 }}</strong>
                        </div>
                        <div class="card">
                            <h3>Açık Görevler</h3>
                            <strong>{{ ($summary['taskCounts']['assigned'] ?? 0) + ($summary['taskCounts']['in_progress'] ?? 0) }}</strong>
                        </div>
                        <div class="card">
                            <h3>Envanter (Aktif)</h3>
                            <strong>{{ $summary['inventoryCounts']['active'] ?? 0 }}</strong>
                        </div>
                        <div class="card">
                            <h3>Toplam Birimler</h3>
                            <strong>{{ $summary['units']->count() }}</strong>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="section-title">Son Olaylar</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Kod</th>
                                <th>Başlık</th>
                                <th>Durum</th>
                                <th>Öncelik</th>
                                <th>Başlangıç</th>
                                <th>Görev Sayısı</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($summary['recentIncidents'] as $incident)
                                <tr>
                                    <td>{{ $incident->code }}</td>
                                    <td>{{ $incident->title }}</td>
                                    <td><span class="badge status-{{ $incident->status }}">{{ strtoupper($incident->status) }}</span></td>
                                    <td>{{ strtoupper($incident->priority) }}</td>
                                    <td>{{ optional($incident->started_at)->timezone($tenant->timezone)->format('d.m.Y H:i') ?? '—' }}</td>
                                    <td>{{ $incident->tasks_count }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="empty-state">Henüz olay bulunmuyor.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>

                <section>
                    <h2 class="section-title">Son Görev Güncellemeleri</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Görev</th>
                                <th>Olay</th>
                                <th>Birim</th>
                                <th>Durum</th>
                                <th>Güncellendi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($summary['recentTasks'] as $task)
                                <tr>
                                    <td>#{{ $task->id }}</td>
                                    <td>{{ optional($task->incident)->code ?? '—' }}</td>
                                    <td>{{ optional($task->assignedUnit)->name ?? 'Atanmadı' }}</td>
                                    <td><span class="badge status-{{ $task->status }}">{{ strtoupper(str_replace('_', ' ', $task->status)) }}</span></td>
                                    <td>{{ optional($task->updated_at)->timezone($tenant->timezone)->format('d.m.Y H:i') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-state">Görev kaydı bulunamadı.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>

                <section>
                    <h2 class="section-title">Birim Durumu</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Birim</th>
                                <th>Tip</th>
                                <th>Kullanıcı</th>
                                <th>Aktif Görev</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($summary['units'] as $unit)
                                <tr>
                                    <td>{{ $unit->name }}</td>
                                    <td>{{ strtoupper($unit->type) }}</td>
                                    <td>{{ $unit->users_count }}</td>
                                    <td>{{ $unit->active_tasks_count }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="empty-state">Tenant için birim kaydı bulunamadı.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>
            @endif
        </div>
        <footer>
            TUDAK OpsCenter prototip paneli — Laravel 11 & Map tabanlı altyapı için backend veri önizlemesi.
        </footer>
    </body>
</html>
