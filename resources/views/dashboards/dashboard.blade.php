<x-app-layout :assets="$assets ?? []">
   <div class="row">
      <div class="col-md-12 col-lg-12">
         <div class="row row-cols-1">
            <div class="d-slider1 overflow-hidden ">
               <ul  class="swiper-wrapper list-inline m-0 p-0 mb-2">
                  <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="700">
                     <div class="card-body">
                        <div class="progress-widget">
                           <div id="circle-progress-01" class="circle-progress-01 circle-progress circle-progress-primary text-center" data-min-value="0" data-max-value="100" data-value="{{ $persentaseSasaranDiregister }}" data-type="percent">
                              <svg class="card-slie-arrow " width="24" height="24px" viewBox="0 0 24 24">
                                 <path fill="currentColor" d="M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z" />
                              </svg>
                           </div>
                           <div class="progress-detail">
                              <p  class="mb-2">Diregister</p>
                              <h4 class="counter" style="visibility: visible;">{{ $jumlahSasaranDiregister }}</h4>
                           </div>
                        </div>
                     </div>
                  </li>
                  <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="800">
                     <div class="card-body">
                        <div class="progress-widget">
                           <div id="circle-progress-02" class="circle-progress-01 circle-progress circle-progress-info text-center" data-min-value="0" data-max-value="100" data-value="{{ $persentaseSasaranDiperiksa }}" data-type="percent">
                              <svg class="card-slie-arrow " width="24" height="24" viewBox="0 0 24 24">
                                 <path fill="currentColor" d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z" />
                              </svg>
                           </div>
                           <div class="progress-detail">
                              <p  class="mb-2">Diperiksa</p>
                              <h4 class="counter">{{ $jumlahSasaranDiperiksa }}</h4>
                           </div>
                        </div>
                     </div>
                  </li>
                  <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="900">
                     <div class="card-body">
                        <div class="progress-widget">
                           <div id="circle-progress-03" class="circle-progress-01 circle-progress circle-progress-primary text-center" data-min-value="0" data-max-value="100" data-value="{{ $persentaseSasaranKonsultasi }}" data-type="percent">
                              <svg class="card-slie-arrow " width="24" viewBox="0 0 24 24">
                                 <path fill="currentColor" d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z" />
                              </svg>
                           </div>
                           <div class="progress-detail">
                              <p  class="mb-2">Konsultasi</p>
                              <h4 class="counter">{{ $jumlahSasaranKonsultasi }}</h4>
                           </div>
                        </div>
                     </div>
                  </li>                 
               </ul>
               <div class="swiper-button swiper-button-next"></div>
               <div class="swiper-button swiper-button-prev"></div>
            </div>
         </div>
      </div>
      <div class="col-md-12 col-lg-8">
         <div class="row">
             {{-- BARIS 1, KOLOM 1: DIAGRAM IMT --}}
             <div class="col-md-12 col-lg-6">
                 <div class="card" data-aos="fade-up" data-aos-delay="1000">
                     <div class="card-header">
                         <h4 class="card-title">Diagram Status Gizi (IMT)</h4>
                     </div>
                     <div class="card-body">
                         <div class="d-flex align-items-center justify-content-between">
                             <div id="imtChart" class="col-8 myChart"></div>
                             <div class="d-grid gap col-4">
                                 @forelse($imtChartLabels as $index => $label)
                                     <div class="d-flex align-items-start">
                                         <svg class="mt-2" width="14" viewBox="0 0 24 24" fill="{{ $chartColors[$index % count($chartColors)] }}"><circle cx="12" cy="12" r="8"></circle></svg>
                                         <div class="ms-3"><span class="text-secondary">{{ $label }}</span><h6>{{ $imtChartData[$index] }}</h6></div>
                                     </div>
                                 @empty
                                     <p class="text-secondary">Tidak ada data.</p>
                                 @endforelse
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

             {{-- BARIS 1, KOLOM 2: DIAGRAM TENSI --}}
             <div class="col-md-12 col-lg-6">
                 <div class="card" data-aos="fade-up" data-aos-delay="1100">
                     <div class="card-header">
                         <h4 class="card-title">Diagram Tensi</h4>
                     </div>
                     <div class="card-body">
                         <div class="d-flex align-items-center justify-content-between">
                             <div id="tensiChart" class="col-8 myChart"></div>
                             <div class="d-grid gap col-4">
                                 @forelse($tensiChartLabels as $index => $label)
                                     <div class="d-flex align-items-start">
                                         <svg class="mt-2" width="14" viewBox="0 0 24 24" fill="{{ $chartColors[$index % count($chartColors)] }}"><circle cx="12" cy="12" r="8"></circle></svg>
                                         <div class="ms-3"><span class="text-secondary">{{ $label }}</span><h6>{{ $tensiChartData[$index] }}</h6></div>
                                     </div>
                                 @empty
                                     <p class="text-secondary">Tidak ada data.</p>
                                 @endforelse
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

             {{-- BARIS 2, KOLOM 1: DIAGRAM KOLESTEROL --}}
             <div class="col-md-12 col-lg-6">
                 <div class="card" data-aos="fade-up" data-aos-delay="1200">
                     <div class="card-header">
                         <h4 class="card-title">Diagram Kolesterol</h4>
                     </div>
                     <div class="card-body">
                         <div class="d-flex align-items-center justify-content-between">
                             <div id="kolesterolChart" class="col-8 myChart"></div>
                             <div class="d-grid gap col-4">
                                 @forelse($kolesterolChartLabels as $index => $label)
                                      <div class="d-flex align-items-start">
                                         <svg class="mt-2" width="14" viewBox="0 0 24 24" fill="{{ $chartColors[$index % count($chartColors)] }}"><circle cx="12" cy="12" r="8"></circle></svg>
                                         <div class="ms-3"><span class="text-secondary">{{ $label }}</span><h6>{{ $kolesterolChartData[$index] }}</h6></div>
                                     </div>
                                 @empty
                                     <p class="text-secondary">Tidak ada data.</p>
                                 @endforelse
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

             {{-- BARIS 2, KOLOM 2: DIAGRAM ASAM URAT --}}
             <div class="col-md-12 col-lg-6">
                 <div class="card" data-aos="fade-up" data-aos-delay="1300">
                     <div class="card-header">
                         <h4 class="card-title">Diagram Asam Urat</h4>
                     </div>
                     <div class="card-body">
                         <div class="d-flex align-items-center justify-content-between">
                             <div id="asamUratChart" class="col-8 myChart"></div>
                             <div class="d-grid gap col-4">
                                 @forelse($asamUratChartLabels as $index => $label)
                                      <div class="d-flex align-items-start">
                                         <svg class="mt-2" width="14" viewBox="0 0 24 24" fill="{{ $chartColors[$index % count($chartColors)] }}"><circle cx="12" cy="12" r="8"></circle></svg>
                                         <div class="ms-3"><span class="text-secondary">{{ $label }}</span><h6>{{ $asamUratChartData[$index] }}</h6></div>
                                     </div>
                                 @empty
                                     <p class="text-secondary">Tidak ada data.</p>
                                 @endforelse
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         
      </div>
      <div class="col-md-12 col-lg-4">
         <div class="row">
            <div class="col-md-6 col-lg-12">               
               {{-- GANTI KARTU STATIS "WEBSITE VISITORS" DENGAN BLOK DINAMIS INI --}}
               <div class="card" data-aos="fade-up" data-aos-delay="300">
                   <div class="card-body d-flex justify-content-around text-center">
                       <div>
                           {{-- Nilai dinamis untuk sisi kiri --}}
                           <h2 class="mb-2">{{ $cardKiriNilai }}</h2>
                           {{-- Judul dinamis untuk sisi kiri --}}
                           <p class="mb-0 text-secondary">{{ $cardKiriJudul }}</p>
                       </div>
                       <hr class="hr-vertial">
                       <div>
                           {{-- Nilai dinamis untuk sisi kanan --}}
                           <h2 class="mb-2">{{ $cardKananNilai }}</h2>
                           {{-- Judul dinamis untuk sisi kanan --}}
                           <p class="mb-0 text-secondary">{{ $cardKananJudul }}</p>
                       </div>
                   </div>
               </div>
            </div>

            {{-- GANTI SELURUH KARTU "ACTIVITY OVERVIEW" DENGAN BLOK INI --}}
            <div class="col-md-12 col-lg-12">
                <div class="card" data-aos="fade-up" data-aos-delay="400">
                    <div class="card-header d-flex justify-content-between flex-wrap">
                        <div class="header-title">
                            <h4 class="card-title mb-2">Aktivitas Terbaru</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse ($aktivitasTerbaru as $aktivitas)
                            <div class="d-flex profile-media align-items-top mb-2">
                                <div class="profile-dots-pills border-primary mt-1"></div>
                                <div class="ms-4">
                                    <h6 class="mb-1">{{ $aktivitas->teks }}</h6>
                                    {{-- Menggunakan Carbon untuk format tanggal yang ramah --}}
                                    <span class="mb-0">{{ \Carbon\Carbon::parse($aktivitas->tanggal)->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center">
                                <p class="text-secondary">Belum ada aktivitas terbaru.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

         </div>
      </div>
   </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        const chartColors = @json($chartColors);

        // Fungsi bantu untuk membuat chart (agar tidak repetitif)
        const createDonutChart = (elementId, labels, data) => {
            if (document.getElementById(elementId) && data.length > 0) {
                const options = {
                    series: data,
                    labels: labels,
                    colors: chartColors,
                    chart: { height: 250, type: 'donut' },
                    plotOptions: { pie: { donut: { size: '55%' } } },
                    legend: { show: false }
                };
                const chart = new ApexCharts(document.querySelector("#" + elementId), options);
                chart.render();
            }
        };

        // Buat semua chart
        createDonutChart('imtChart', @json($imtChartLabels), @json($imtChartData));
        createDonutChart('tensiChart', @json($tensiChartLabels), @json($tensiChartData));
        createDonutChart('kolesterolChart', @json($kolesterolChartLabels), @json($kolesterolChartData));
        createDonutChart('asamUratChart', @json($asamUratChartLabels), @json($asamUratChartData));
    });
</script>
@endpush

</x-app-layout>
