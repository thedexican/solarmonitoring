<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kwh extends MY_Controller {

	private $sitePubID;
	private $siteID;

	function __construct()
    {
       parent::__construct();
       $this->load->model('m_site');
	 	$this->sitePubID = $this->m_site->get_default_public_siteId();
		$this->siteID = $this->m_site->get_siteId($this->sitePubID);
        
    }
	public function index()
	{
		
		redirect('/technical-solar-data/kwh/day');


	}
	public function day($date = null)
	{
		
		
		if (mycheckdate($date))
		{
			$day= $date;
			$api_url = "api/kwh/day/date/" . $day . "/siteID/" . $this->sitePubID; 
			$today = 'false';
		}
		else
		{
			// default to today()
			$day = date("Y-m-d"); 
			$api_url = "api/kwh/day/date/" . $day . "/siteID/" . $this->sitePubID; 
			$today = 'true';
			

			
		}

		$this->load->model('m_kwh');
		$data['kwh'] = $this->m_kwh->get_kwh_detail($this->siteID, $day, 'day');
		$data['page'] = 'solardata/v_kwh';
		$data['chartdatelabel'] = myDateInterval($day, 'day');
		$data['period'] = 'day';
		$data['api']= array("url" => $api_url, 
							"success" => "grabAndFillKwh(data, 'hour', $today);",
							"complete" => "buildBigChart('kWh', 'day', 2);");
							
		$data['widgets'] = array('alerts', 'peak-power', 'battery-history');
		$this->load->view('includes/template', $data);
		
	}
	public function week($date = null)
	{
		if (mycheckdate($date))
		{
			$day= $date;
			$api_url = "api/kwh/week/date/" . $day . "/siteID/" . $this->sitePubID; 
		}
		else
		{
			// default to today()
			$day = date("Y-m-d"); 
			$api_url = "api/kwh/week/siteID/" . $this->sitePubID; 

			
		}

		$this->load->model('m_kwh');
		//$data['detailarray'] = $this->m_metrics->get_out_volt_amps_detail($this->siteID, $day);
		$data['kwh'] = $this->m_kwh->get_kwh_detail($this->siteID, $day, 'week');
		$data['period'] = 'week';
		$data['page'] = 'solardata/v_kwh';
		$data['chartdatelabel'] = myDateInterval($day, 'week');
		$data['api']= array("url" => $api_url, 
							"success" => "grabAndFillKwh(data, 'week');",
							"complete" => "buildBigChart('kWh', 'week', 2);");
		$data['widgets'] = array('alerts', 'peak-power', 'battery-history');
		$this->load->view('includes/template', $data);
		

	}
	public function month($date = null)
	{
		if (mycheckdate($date))
		{
			$day= $date;
			$api_url = "api/kwh/month/date/" . $day . "/siteID/" . $this->sitePubID; 
			
		}
		else
		{
			// default to today()
			$day = date("Y-m-d"); 
			$api_url = "api/kwh/month/date/" . $day . "/siteID/" . $this->sitePubID; 

			
		}

		$this->load->model('m_kwh');
		//$data['detailarray'] = $this->m_metrics->get_out_volt_amps_detail($this->siteID, $day);
		$data['kwh'] = $this->m_kwh->get_kwh_detail($this->siteID, $day, 'month');
		$data['period'] = 'month';
		$data['page'] = 'solardata/v_kwh';
		$data['chartdatelabel'] = myDateInterval($day, 'month');
		$data['api']= array("url" => $api_url, 
							"success" => "grabAndFillKwh(data, 'month');",
							"complete" => "buildBigChart('kWh', 'month', 2);");
		$data['widgets'] = array('alerts', 'peak-power', 'battery-history');
		$this->load->view('includes/template', $data);
		
	}
	public function year($year = null)
	{
		if (isset($year))
		{
			// check that it's a valid #
			$day = $year . "-01-01"; 
			$api_url = "api/kwh/year/date/" . $day . "/siteID/" . $this->sitePubID; 
		}
		else
		{
			// default to current year
			
			$day = date("Y-m-d"); 
			$api_url = "api/kwh/year/date/" . $day . "/siteID/" . $this->sitePubID; 

			
		}

		$this->load->model('m_kwh');
		
		$data['kwh'] = $this->m_kwh->get_kwh_detail($this->siteID, $day, 'year');
		$data['period'] = 'year';
		$data['page'] = 'solardata/v_kwh';
		$data['chartdatelabel'] = myDateInterval($day, 'year');
		$data['api']= array("url" => $api_url, 
							"success" => "grabAndFillKwh(data, 'year');",
							"complete" => "buildBigChart('kWh', 'year', 2);");
		$data['widgets'] = array('alerts', 'peak-power', 'battery-history');
		$this->load->view('includes/template', $data);
		
	}
	public function since_inception()
	{
		$day = date("Y-m-d"); 
		$api_url = "api/kwh/since_inception/siteID/" . $this->sitePubID; 
		
		$this->load->model('m_kwh');
		$data['kwh'] = $this->m_kwh->get_kwh_detail($this->siteID, $day, 'inception');
		$data['period'] = 'inception';
		$data['page'] = 'solardata/v_kwh';
		$data['chartdatelabel'] = myDateInterval($day, 'inception');
		$data['api']= array("url" => $api_url, 
							"success" => "grabAndFillKwh(data, 'inception');",
							"complete" => "buildBigChart('kWh', 'inception', 2);");
		$data['widgets'] = array('alerts', 'peak-power', 'battery-history');
		$this->load->view('includes/template', $data);
		
	}

	public function download()
	{
		$this->load->dbutil();
		$query = $this->db->query("select created_date, port, out_i as out_current, in_i as in_current, batt_v as battery_voltage, 
			in_v as pv_voltage, out_kwh, out_ah from export_data where site_id=$this->siteID");

				//write csv data
		$data = $this->dbutil->csv_from_result($query);
		//create random file name
		$name = 'data_'.date('d-m-y-s').'.csv';
		$this->load->helper('file');
		if ( ! write_file('./csv/'.$name, $data))
		{
		     echo 'Unable to write the CSV file';
		}
		else
		{
		    //perform download
		    $file = file_get_contents("./csv/".$name); // Read the file's contents
		    $filename = 'solar_data_'.date('d-m-y').'.csv';
		    force_download($filename, $file);
		}

	}

	private function firstDayOfMonth($uts=null) 
	{ 
    $today = is_null($uts) ? getDate() : getDate($uts); 
    $first_day = getdate(mktime(0,0,0,$today['mon'],1,$today['year'])); 
    return $first_day[0]; 
	}

	/*
		private function myprepdate($date)
	{
			$qday = date_parse($date);
			$d = array();
			$d['year'] = $qday['year'];
    		$d['month'] = $qday['month'];
    		$d['day'] = $qday['day'];
    		return $d;
	}
	public function solardata()
	{
		$tab = 'kwh';
		if ($tab == 'kwh')
		{
			// fix this later
			$siteid = 1;
			$this->load->model('m_metrics');
			$data['kwh'] = $this->m_metrics->get_day_kwh($siteid);
		}
		
		$data['page'] = 'v_solar-data';
		$data['include'] = 'js/solar-data.js';
		$this->load->view('includes/template', $data);
	}

	
	
	public function test()
	{
		$today= getdate();
    	$year = $today['year'];
    	$month = $today['mon'];
    	$day = $today['mday'];
    	//put date request into unix form  
    	$start= mktime(0, 0, 0, $month, $day, $year);
    	$end= mktime(23, 59, 59, $month, $day, $year);
    	$siteid = 1;
    	
    	$this->db->select('timestamp, out_i*batt_v AS kw');
    	$this->db->where('site_id', $siteid);
    	$query = $this->db->get('export_data');
    	
		//$sql = "select `timestamp`, `out_i`*`batt_v` AS `kw` from export_data where `site_id`=$siteid and `timestamp` >= FROM_UNIXTIMESTAMP($start)";
		//$sql = "select timestamp, out_i as kw from export_data where site_id=$siteid and timestamp >= UNIX_TIMESTAMP($start) AND timestamp <= UNIX_TIMESTAMP($end)";
		$sql = "select timestamp, out_i*batt_v as kw from export_data where site_id=$siteid and timestamp >= $start and timestamp <= $end";
		echo $sql;
		$query = $this->db->query($sql);
		/*
		$this->db->select('timestamp, out_i*batt_v as kw');
		$this->db->from('export_data');
		$query = $this->db->get();
		
		//$result = $query->result_array();
		foreach ($query->result() as $row)
		{
			echo $row->timestamp;
			echo $row->kw;
			
		}
		
		
	}
	*/


}

