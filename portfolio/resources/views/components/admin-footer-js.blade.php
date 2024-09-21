

<!-- Scripts below are for demo only -->
<script type="text/javascript" src="{{asset('Admin/js/main.min.js')}}?v=1628755089081"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script type="text/javascript" src="{{asset('Admin/js/chart.sample.min.js')}}"></script>

<!--show image -->
<script>
    function showModal(img) {
        var modal = document.getElementById("myModal");
        var modalImg = document.getElementById("imgModal");
        var captionText = document.getElementById("caption");

        modal.style.display = "block";
        modalImg.src = img.src;
        captionText.innerHTML = img.alt;

        // Đóng modal khi ấn vào nút "X"
        var span = document.getElementsByClassName("close")[0];
        span.onclick = function() {
            modal.style.display = "none";
        }
    }
</script>
<!--show alter-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var successAlert = document.getElementById('successAlert');
        var errorAlert = document.getElementById('errorAlert');

        // Hiện thông báo thành công
        if (successAlert) {
            setTimeout(function() {
                successAlert.classList.add('fade-out');
                setTimeout(function() {
                    successAlert.style.display = 'none';
                }, 1000); // Thời gian hiệu ứng fade-out
            }, 3000); // Thời gian hiển thị thông báo
        }

        // Hiện thông báo lỗi
        if (errorAlert) {
            setTimeout(function() {
                errorAlert.classList.add('fade-out');
                setTimeout(function() {
                    errorAlert.style.display = 'none';
                }, 1000); // Thời gian hiệu ứng fade-out
            }, 3000); // Thời gian hiển thị thông báo
        }
    });

</script>
<!--add image and show-->
<script>function displayThumbnail(input) {
        var thumbnailImage = document.getElementById('thumbnailImage');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                thumbnailImage.src = e.target.result;
                thumbnailImage.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }</script>
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '658339141622648');
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=658339141622648&ev=PageView&noscript=1"/></noscript>

<!-- Icons below are for demo only. Feel free to use any icon pack. Docs: https://bulma.io/documentation/elements/icon/ -->
<link rel="stylesheet" href="https://cdn.materialdesignicons.com/4.9.95/css/materialdesignicons.min.css">
