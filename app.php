<?php
	/**
	 * classe de dashboard
	 */
	class Dashboard {
		private $data_inicio;
		private $data_fim;
		private $numeroVendas;
		private $totalVendas;

	    public function __construct() {
	    
	    }

	    public function __get($attr) {
	    	return $this->$attr;
	    }

	    public function __set($attr, $val) {
	    	$this->$attr = $val;
	    }
	}

	/**
	 * classe de conexão com o banco
	 */
	class Conexao {
		private $host = 'localhost';
		private $dbname = 'dashboard'; 
		private $user = 'root'; 
		private $pass = ''; 

	    public function conectar() {
	    	try {
	    		$conexao = new PDO("mysql:host=$this->host;dbname=$this->dbname", "$this->user", "$this->pass");

	    		//instência da conexão tabalhe com UTF8
	    		$conexao->exec('set charset set utf8');

	    		return $conexao;
	    	} catch (PDOException $e) {
	    		echo '<p> Erro ao conectar com o banco !!! Erro: ' . $e->getMenssege();
	    	}
	    }
	}

	/**
	 * classe (model)
	 */
	class Bd {
	    private $conexao;
	    private $dashboard;

	    public function __construct(Conexao $conexao, Dashboard $dashboard) {
	        $this->conexao = $conexao->conectar(); //recebendo a instância da conexão do banco !
	        $this->dashboard = $dashboard;
	    }

	    public function getNumeroVendas() {
	    	$stmt = $this->conexao->prepare('select count(*) as numero_vendas from tb_vendas where data_venda between ? and ?');
	    	$stmt->bindValue(1, $this->dashboard->__get('data_inicio'));
	    	$stmt->bindValue(2, $this->dashboard->__get('data_fim'));
	    	$stmt->execute();

	    	return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
	    }

	    public function getTotalVendas() {
	    	$stmt = $this->conexao->prepare('select SUM(total) as total_vendas from tb_vendas where data_venda between ? and ?');
	    	$stmt->bindValue(1, $this->dashboard->__get('data_inicio'));
	    	$stmt->bindValue(2, $this->dashboard->__get('data_fim'));
	    	$stmt->execute();

	    	return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
	    }
	}

	//capturando os valores da URL
	$competencia = explode('-', $_POST['competencia']);
	$ano = $competencia[0];
	$mes = $competencia[1];

	//(calendario, mes, ano) = quantos dias existe naquele mês daquele ano
	$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

	$conexao = new Conexao();
	$dashboard = new Dashboard();

	//setando data_inicio e data_fim
	$dashboard->__set('data_inicio', "$ano-$mes-01");
	$dashboard->__set('data_fim', "$ano-$mes-$dias_do_mes");


	$bd = new Bd($conexao, $dashboard);

	$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
	$dashboard->__set('totalVendas', $bd->getTotalVendas());

	//header('Content-Type: application/json');
	echo json_encode($dashboard);
?>