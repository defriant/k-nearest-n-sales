chartPendapatan()

function chartPendapatan() {
    let mychart
    $.ajax({
        type:'get',
        url:'/data-pendapatan',
        success:function(response){
            console.log(response)
            let ctx = document.getElementById("data-penjualan-chart").getContext('2d')
            mychart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: response.pendapatan,
                            borderColor: mainColor,
                            backgroundColor: mainColor
                        }
                    ]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    })

    
    $('.change-periode').on('click', function(){
        $('.change-periode').removeClass('active')
        $(this).addClass('active')
        let periode = $(this).data('periode')
        updateChartPendapatan(mychart, periode)
    })
}

function updateChartPendapatan(mychart, periode) {
    $.ajax({
        type:'get',
        url:'/data-pendapatan?tahun='+periode,
        success:function(response){
            $('#terjual').html(response.terjual)
            $('#totalPendapatan').html(response.totalPendapatan)
            mychart.data = {
                labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: response.pendapatan,
                        borderColor: mainColor,
                        backgroundColor: mainColor
                    }
                ]
            }
            mychart.update()
        }
    })
}