<?php

namespace Cibilia\Dropshipextend\Controller\Attribute;


class Manuall extends \Magento\Framework\App\Action\Action {

	protected $resultJsonFactory;

	public function _constuct(
			\Magento\Framework\App\Action\Context $context,  
			\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory      
		){
    		$this->resultJsonFactory = $resultJsonFactory;
    		parent::__construct($context);
	}

	public function execute(){

		{
			$attribute_arr = array("Abaza","Abbaye de Belloc","Abbaye de Tamié","Abertam","Abondance","Ädelost","Adobera","Adygheysky","Afuega'l pitu","Akkawi","Allgäuer Bergkäse","Altaysky","Anari","Añejo","Antep (Gaziantep)","Anthotyros","Appenzeller","Ardrahan","Areesh","Armola (Seferihisar)","Arnavut","Arseniko Naxou (Naxos)","Asadero","Asiago","Atleet","Aura","Ayibe","Bachensteiner","Bałtycki","Bandel","Banon","Basket","Bastardo del Grappa","Batzos,","Beaufort","Bella Lodi","Belo Sirenje","Bergenost","Bergkäse","Berner Alpkäse","Beyaz peynir","Bilozhar","Bitto","Blå Gotland","Bleu d'Auvergne","Bleu de Bresse","Bleu de Gex","Bleu des Causses","Bleu du Vercors-Sassenage","Bohinc Jože","Bonifaz","Boursin","Bra","Brânzǎ de burduf","Brânză de vaci","Brick","Brie de Meaux","Brie de Melun","Brie Noir","Brillat-Savarin","Brimsen","Brocciu","Brös","Brunost","Brussels","Bryndza","Bryndza Podhalańska","Bucheron","Bukovinskyi","Bundz","Burrata","Bursztyn","Butterkäse","Byaslag","Cabécou","Cabrales","Čačanski Kajmak","Čačanski Sir","Cacio figurato","Caciocavallo","Caciotta","Cambozola","Camembert de Normandie","Çamur (İzmir)","Cancoillotte","Cantal","Caprino","Caravane","Carré de l'Est","Caş","Caşcaval","Casciotta d'Urbino","Castelmagno","Castelo Branco","Cathare","Catupiry","Cazelle de Saint Affrique","Çerkez Füme","Chabichou du Poitou","Chamois d'Or","Chanco","Chaource","Chaqueño","Chaumes","Chechil","Cheddar","Cherni Vit","Chèvre Metsovou (Metsovo)","Chevrotin","Chhena","Chhurpi","Chiapas","Chihuahua","Chimays","Chloro","Chura kampo","Chura loenpa","Chyorny Altai","Circassian","Civil (Erzurum)","Çökelek","Colby","Colby-Jack","Colorado Blackie","Çömlek (Kayseri)","Comté","Cotija","Cougar Gold","Coulommiers","Coutances","Criollo","Criollo","Crottin de Chavignol","Cuajada	Cup","Dahi Chhena","Danbo","Danilovsky","Danish Blue","Délice de Bourgogne","Délice du Calvados","Dil","Dimsi","Dobrodar","Dorozhny","Dziugas","Edamski","Édel de Cléron","Edirne","Eesti Juust","Emmental de Savoie","Emmental français est-central","Emmentaler","Époisses de Bourgogne","Esrom","Explorateur","Ezine (Çanakkale)","Faisselle","Farmer","Feta","Flower of Rajya","Fontina","Formaela","Formaggio di Fossa","Formai de Mut dell'Alta Valle Brembana","Fourme d'Ambert","Fourme de Montbrison","Fromage blanc","Fromager d'Affinois","Froumaela Valmandouras (Peloponnese)","Fynbo","Galotyri (Thessaly, Epirus)","Gamalost","Gamonedo","Gaperon","Garrotxa","Gauda","Ġbejna","Geitost","Gelundener Käse","Golandsky","Gołka","Gorgonzola","Gornoaltaysky","Goya","Gräddost","Grana Padano","Graviera","Gravyer (Kars)","Grevé","Gruyère","Gryficki","Gudbrandsdalsost","Halloumi","Handkäse","Harzer","Hauskyjza","Havarti","Hellim","Herrgårdsost","Homoljski kozji (Homolje goat)","Homoljski kravlji (Homolje cow)","Homoljski ovčiji (Homolje sheep)","Hoop","Hushållsost","Idiazábal","Imsil","Jameed","Jāņi","Jarlsberg","Jibneh Arabieh","Kačkavalj","Kadaka juust","Kalari","Kalathaki, (Limnos)","Kalathotos","Kalimpong","Karaván","Karikeftos","Karish","Kaşar","Kashkaval","Kashkaval","Kashkawan","Kasseri","Katiki, (Domokos)","Keçi peyniri","Kefalograviera","Kefalotyri","Kefalotyri","Kelle Peyniri(Balıkesir)","Kesong puti","Khoya","Kirlihanım (Ayvalık)","Kopanisti","Kopanisti (İzmir)","Korall","Korbáčiky","Kortowski","Koryciński","Kostromskoy","Krasotyri (Kos)","Kravská hrudka","Krivovirski Kačkavalj","Kunik","Küp (Yozgat, Sivas)","La Serena","Labne","Labneh","Ladenios","Ladotyri, (Lesbos)","Langres","Languiole","Lappi","Latvijas","Lavort","Le Wavreumont","Lechicki","Leipäjuusto","L'Etivaz","Liederkranz","Lighvan","Liliput","Limburger","Limburger","Limburger","Lingallin","Liptauer	Liptauer orKőrözött","Livarot","Livno","Lor","Łowicki","Lubuski","Lüneberg","Lužnička Vurda","Macônnais","Majdoule","Malaka (Chania)","Manchego","Manoura Sifnou (Sifnos)","Manouri","Manyas (Balıkesir)","Maredsous","Maribo","Maroilles","Mascarpone","Mastelo (Chios)","Mató","Maytag Blue","Mazurski","Medynsky","Melichloro (Lemnos)","Menonita","Metsovella (Metsovo)","Metsovone","Mihaliç","Milbenkäse","Mimolette","Minas","Mish","Moale","Mohant","Molbo","Mondseer","Mont des Cats","Montafoner Sauerkäse","Montasio","Monte Enebro","Monte Veronese","Monterey Jack","Moose","Morbier","Morlacco","Moskovsky","Motal","Mozzarella di bufala","Muenster","Munster","Murcian","Myzithra","Nabulsi","Nacho","Nanoški","Năsal","Neufchâtel","Nguri","Niolo","Nøkkelost","Norvegia","Oaxaca","Oázis","Obruk (Karaman)","Oka","Omichka","Orda","Örgü (Diyarbakır)","Oscypek","Ossau-Iraty","Oštiepok","Otlu (Van)","Ovčia hrudka","Paddraccio","Pallone di Gravina","Pálpusztai","Paneer","Pannónia","Panquehue","Parenica","Parmigiano-Reggiano","Paški sir","Passendale","Pecorino","Pecorino di Filiano","Pecorino Romano","Pecorino Sardo","Pecorino Siciliano","Pecorino Toscano","Pélardon","Pepper jack","Petroti (Tinos)","Piave","Pichtogalo Chanion (Chania)","Picodon de l'Ardéche","Picón Bejes-Tresviso","Pikantny","Pinconning","Pirotski Kačkavalj","Planinski","Poligny-Saint-Pierre","Pont-l'Évéque","Port Salut","Poshekhonsky","Prästost","Provolone","Przeworski","Pule","Queijo Canastra","Queijo coalho","Queijo Cobocó","Queijo de Colônia","Queijo de Nisa","Queijo do Pico","Queijo do Serro","Queijo Manteiga","Queijo Meia Cura","Queijo prato","Queijo seco","Queijo-do-Reino","Quesillo","Quesillo","Quesillo","Queso blanco","Queso Campesino","Queso Crema","Queso crineja","Queso Cuajada","Queso de cuajo","Queso de mano","Queso Fresco","Queso Llanero	Queso Palmita","Queso telita","Questo Costeño","Queto","Raclette","Radamer","Raejuusto","Ragusano","Raschera","Rauchkäse","Red Hawk","Redykołka","Reggianito","Remoudou","Renaico","Requeijão","Requeijão","Ricotta","Rigotte de Condrieu","Robiola d'Alba","Rocamadour","Rochebarron","Rodoric","Rokpol","Romadur","Roncal","Roquefort","Rosa Camuna","Rossiysky","Roue de Brielove","Roumy","Rubing","Rushan","Saga","Saint Albray","Saint André","Sainte-Maure de Touraine","Saint-Félicien","Saint-Marcellin","Saint-Nectaire","Saint-Paulin","Sakura","Salamura (Bingöl, Tokat)","Salers","Samsø","San Michali","Santarém","São Jorge","Sardo","Sayas (İzmir)","Sbrinz","Scamorza","Schabziger","Seller-sur-Cher","Serra da Estrela","Sfela (Peloponnese)","Shanklish","Sirene","Sjenički","Škripavac","Słupski chłopczyk","Smetankowyi","Snøfrisk","So","Sottocenere al tartufo","Sovetsky","Stamatini","Steirerkäse","Stracchino","Stracciatella di bufala","String","Sulguni","Surti Paneer","Svecia","Svrljiški Belmuz ","Swiss","Syr","Syrian","Taleggio","Tandil","Tarentais","Tasty","Teleme","Telemea","Telli (Karadeniz)","Tête de Moine","Tetilla","Tilsit","Tilsiter","Tolminc","Toma Piemontese","Tomme au Fenouil","Tomme au Revard","Tomme de Bauges","Tomme de Boudane","Tomme de Butone","Tomme de Savoie","Torta del Casar","Touloumisio","Tounjski","Trappista","Travnički (Vlašić)","Tulum","Tupí","Tvaroh","Tvorog(творог)","Twaróg","Tybo","Tylżycki","Tyrolean grey (Tiroler Graukäse)","Uglichsky","Ukraїnskyi","Urda","Urdǎ","Urdă","Užički Kajmak","Vacherin","Vacherin Fribourgeois","Vacherin Mont d'Or","Valdeón","Valençay","Valle d'Aosta Fromadzo","Valtellina Casera","Västerbottensost","Vesterhavsost","Vieux-Boulogne","Vurda","Wagasi","Weisslacker","Xygalo, (Crete)","Xynomizithra","Xynotyro","Yaroslavsky","Zakusochny","Zamorano","Zgorzelecki");
			$attributeCode 	 = 'cheese_family';


			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

			// find all options
			{
				{
					//create that option
					{
						$storeObj = $objectManager
										->create('\Magento\Store\Model\StoreManagerInterface');
						$allStores = $storeObj->getStores();

						$attributeObj = $objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute');
						$attributeInfo = $attributeObj->getCollection()
               						->addFieldToFilter('attribute_code',['eq'=> $attributeCode ])
               						->getFirstItem();

						$option = array();
						$option['attribute_id'] = $attributeInfo->getAttributeId();
						foreach($attribute_arr as $key=>$value){
						    $option['value'][$value][0]=$value;
						    foreach($allStores as $store){
						        $option['value'][$value][$store->getId()] = $value;
						    }
						}

						$eavSetup = $objectManager->create('\Magento\Eav\Setup\EavSetup');
						$eavSetup->addAttributeOption($option);
					}
				}

				// return json in same format
				{
					//$result = $this->resultJsonFactory->create();
					$result = $objectManager->create("\Magento\Framework\Controller\Result\Json");
       				$data = ['message' => 'Attribute updated successfully'];
      				return $result->setData($data);
				}
			}
			
		}

	}

}