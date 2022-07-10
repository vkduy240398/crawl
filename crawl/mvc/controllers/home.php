<?php 
require_once './mvc/functional/ExecuteImage.php';
class home extends controller{
    function __construct()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->ExecuteImages = new ExecuteImages();
        $this->landsModels      = $this->models('landsModels');
        $this->categoryModels   = $this->models('categoryModels');
        $this->areaModels       = $this->models('areaModels');
        $this->regionModels     = $this->models('regionModels');
    }
    function index(){
        $data = [
            'region'    => []
        ];
        $this->view('index', $data);
    }
    function crawl($page_callBack = 1){
        if ($_SERVER['REQUEST_METHOD'] =='POST')
        {
            $limit = 20;
            $page = $page_callBack;
            $offset = ($page - 1) * 20;
            $url = 'https://gateway.chotot.com/v1/public/ad-listing?cg=1000&o='.$offset.'&page='.$page.'&st=u,h&limit='.$limit.'&key_param_included=true';
            $array = [];
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $results = curl_exec($curl);
            curl_close($curl);
            $array = json_decode($results,true);
            $array_results = $array['ads'] ? $array['ads'] : [];
            //    
            $time = date('d-m-Y 23:59:59', time());
            $dataTime = new DateTime($time);
            $timeStamp = $dataTime->getTimestamp() * 1000;
            if (count($array_results) > 0 )
            {
                $countArray = [];
                $array_cate = [];
                $array_area= [];
                $array_region= [];
                $count = 0;
                foreach($array_results as $key => $val)
                {
                    if ($val['list_time'] <= $timeStamp) {
                        $count++;
                        $lands = $this->landsModels->select_array('*',['list_id' => $val['list_id']]);
                        $cate = $this->categoryModels->select_array('*',['idCrawl' => $val['category']]);
                        $area = $this->areaModels->select_array('*',['idCrawl' => $val['area']]);
                        $region = $this->regionModels->select_array('*',['idCrawl' => $val['region']]);
                        if (count($cate) <= 0)
                        {
                            $array_cate[$val['category']] = [
                                'idCrawl'       => $val['category'],
                                'name'          => $val['category_name'],
                                'created_at'    => gmdate('Y-m-d H:i:s',time() + 7 *3600)   
                            ];
                        }
                        if (count($region) <= 0)
                        {
                            $array_region[$val['region']] = [
                                'idCrawl'       => $val['region'],
                                'name'          => $val['region_name'],
                                'created_at'    => gmdate('Y-m-d H:i:s',time() + 7 *3600)   
                            ];
                        }
                        if (count($area) <= 0) 
                        {
                            $array_area[$val['area']] = [
                                'idCrawl'       => $val['area'],
                                'name'          => $val['area_name'],
                                'created_at'    => gmdate('Y-m-d H:i:s',time() + 7 *3600)   
                            ];
                        }
                        if (count($lands) <= 0)
                        {
                            
                            if (isset($val['image'])) {
                                $this->ExecuteImages->copyImages(isset($val['image']) ? $val['image'] : NULL, explode('/', $val['image'])[6]);
                            }
                            $fp = fopen('public/' . "myText.txt","wb");
                            $contents = $val['body'];
                            $fp = fopen('public/'.$val['list_id'].'.txt','wb');
                            fwrite($fp, $contents);
                            array_push($countArray, [
                                'list_id'       => $val['list_id'],
                                'list_time'     => $val['list_time'],
                                'region'        => $val['region'],
                                'subject'       => $val['subject'],
                                'body'          => $val['body'],
                                'category'      => $val['category'],
                                'area'          => $val['area'],
                                'price'         => $val['price'],
                                'image'         => isset($val['image']) ? explode('/', $val['image'])[6] : '',
                                'toilets'       => isset($val['toilets'])?$val['toilets'] : '',
                                'address'       => isset($val['address'])  ? $val['address'] : '',
                                'location'      => isset($val['location']) ? $val['location'] : '',
                                'params'        => json_encode($val['params']),
                                'created_at'    => gmdate('Y-m-d H:i:s',time() + 7 *3600)   
                            ]);
                        }
                    }
                }
                $this->landsModels->insertMultiple($countArray);
                $this->categoryModels->insertMultiple(array_values($array_cate));
                $this->areaModels->insertMultiple(array_values($array_area));
                $this->regionModels->insertMultiple(array_values($array_region));
                $limits = $_POST['page'] ? $_POST['page'] : 1;
                if ($count >= 20 && $page <=  $limits)
                {   
                    $this->crawl($page + 1);
                }
            }
        }
      
    }
    function clean(){
     
       if ($_GET['region'])
       {
            $data = $this->landsModels->select_array('*',['region !=' => $_GET['region']]);
            foreach($data as $key => $val){
                if (file_exists('public/images/'.$val['image']))
                {
                    unlink('public/images/'.$val['image']);
                }
                if (file_exists('public/'.$val['list_id'].'.txt'))
                {
                    unlink('public/'.$val['list_id'].'.txt');
                }
            }
            $results = $this->landsModels->delete(['region !=' => $_GET['region']]);
            $datas =  $this->landsModels->select_array('*');
            if ($results['type'] === "sucessFully")
            {
              
                echo json_encode([
                    'code'      => 200,
                    'message'   => 'Filter successfully',
                    'datas'     => $datas
                ]);    
            }
       }
    }
    function getAll(){
        $datas =  $this->landsModels->select_array('*');
        echo json_encode([
            'datas'     => $datas
        ]);
    }
}