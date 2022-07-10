<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="http://localhost/crawl/">
    <link rel="stylesheet" href="public/style.css">
    <title>Document</title>
</head>
<body>
   <div class="container">
        <div class="filter__crawl">
            <select name="region" id="">
                <?php if(isset($region) && $region != NULL){?>
                    <?php foreach($region as $key => $val){?>
                        <option value="<?= $val['idCrawl'] ?>"><?= $val['name'] ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <button class="filter"><span class='filter__text'>Filter</span>
                <div class="loadingCrawl2">
                    <span class="loading" style="--i: 1"></span>
                    <span class="loading" style="--i: 2"></span>
                    <span class="loading" style="--i: 3"></span>
                    <span class="loading" style="--i: 4"></span>
                    <span class="loading" style="--i: 5"></span>
                    <span class="loading" style="--i: 6"></span>
                    <span class="loading" style="--i: 7"></span>
                    <span class="loading" style="--i: 8"></span>
                    <span class="loading" style="--i: 9"></span>
                </div>
            </button>
        </div>
        <div class="crawl__div">
            <input type="number" name="page" placeholder ="Nhập giới hạn số trang cần lấy: ví dụ 5, sẽ lấy từ 1 đến 5 trang">
            <button class="crawl">
                <span class="text">
                    Crawl
                </span>
                <div class="loadingCrawl">
                    <span class="loading" style="--i: 1"></span>
                    <span class="loading" style="--i: 2"></span>
                    <span class="loading" style="--i: 3"></span>
                    <span class="loading" style="--i: 4"></span>
                    <span class="loading" style="--i: 5"></span>
                    <span class="loading" style="--i: 6"></span>
                    <span class="loading" style="--i: 7"></span>
                    <span class="loading" style="--i: 8"></span>
                    <span class="loading" style="--i: 9"></span>
                </div>
            </button>
        </div>
        <div class="row">
            
        </div>
   </div>
</body>
<script>
    const button = document.querySelector('button.filter');
    const crawl = document.querySelector('button.crawl');
    const span = document.querySelector('span.text');
    const span_filter = document.querySelector('span.filter__text');
    const loadingCrawl = document.querySelector('.loadingCrawl');
    const loadingCrawl_2 = document.querySelector('.loadingCrawl2');
    const xhr = new XMLHttpRequest();
    button.addEventListener('click',(e) =>{
       const region = document.querySelector('select[name="region"]').value;
       xhr.open('GET',`clean-data.html?region=${region}`);
       loadingCrawl_2.style.display  = 'unset';
       xhr.onload = function(response){
        if (response.target.status === 200)
            {
                const data = JSON.parse(this.responseText)
                span_filter.innerHTML = 'Filter';
                loadingCrawl_2.style.display  = 'none';
                e.target.disabled = false;
                const row = document.querySelector('.row');
                row.innerHTML = '';
                if (data.datas.length > 0)
                {
                    data.datas.map((v) => {
                        const col = document.createElement('div');
                        col.setAttribute('class','col');
                        const image = document.createElement('img');
                        image.setAttribute('src', 'public/images/'+v.image);
                        col.append(image);
                        row.appendChild(col)
                    });
                }
                alert(data.message);
            }
       }
       xhr.send();
    }); 

    crawl.addEventListener('click',(e) => {
        const page = document.querySelector('input[name="page"]').value;
        console.log(page);
        e.target.disabled = true;
        span.innerHTML = '';
        loadingCrawl.style.display  = 'unset';
        xhr.open('POST',`crawl-web.html`);
        xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
        xhr.onload = function(response){
            if (response.target.status === 200)
            {
                span.innerHTML = 'Crawl';
                loadingCrawl.style.display  = 'none';
                e.target.disabled = false;
                const isConfirm = confirm('View data took');
                if (isConfirm) {
                    xhr.open('GET','get-data.html');
                    xhr.onload = function(response){
                        const data = JSON.parse(this.responseText);
                        const row = document.querySelector('.row');
                        if (data.datas.length > 0)
                        {
                            data.datas.map((v) => {
                                const col = document.createElement('div');
                                col.setAttribute('class','col');
                                const image = document.createElement('img');
                                image.setAttribute('src', 'public/images/'+v.image);
                                col.append(image);
                                row.appendChild(col);
                            });
                        }
                    }
                    xhr.send();
                }
            }
        }
        xhr.send('page=' + page);
    });
</script>
</html>