//####################################################################################################################

function getRandomIntInclusive(min, max)
{
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max-min+1)) + min; //The min and the max is inclusive 
}

//####################################################################################################################

function createUnorderedNumber(from_x,to_y)
{
  from_x = Math.ceil(from_x);
  to_y = Math.floor(to_y);
  nNum = parseFloat(to_y)-parseFloat(from_x)+1;

  numberlist = [];

  getN = 0;
  for (b=0; b<nNum; b++)
  {
  	cobaAmbil = true;
  	while (cobaAmbil)
  	{
  		getN = getRandomIntInclusive(from_x,to_y);
  		cekdulu = numberlist.indexOf(getN);
  		if (cekdulu >-1) {cobaAmbil = true;} else {cobaAmbil = false;}
  	}
  	numberlist.push(getN);
  }
  return numberlist;
}

//####################################################################################################################

function acakinSoal(soalPerBS,nSoal,susunanSoal)
{
	var itemPerBS = soalPerBS;
	var jmlItem = nSoal;
	var susunanSoalTerformat = susunanSoal;

	var soalTeracak = [];
	var itemSoalTerpakai = [];

	var numBS = 0;
	var acakPerBS = [];
	var soalPerBS = [];

	var numBS = susunanSoal.replace(/[^+]/g, "").length;

	for (ac=0; ac<jmlItem; ac++)
	{
		soalTeracak[ac]='';
	}

	if (numBS==0)		//jika hanya satu bidang studi, bisa langsung aja
	{
		itemSoalTerformat = susunanSoalTerformat.split(",");
		nSoalTerformat = itemSoalTerformat.length;				//hitung berapa banyak jadinya item soal terformat,
																//contoh : 1-2,3,4f,5-7 maka nSoalTerformat adalah 4
			//########################################################################
			//########## taruh / susun dulu yg berformat f (fixed position) ##########
			for (fi=0; fi<nSoalTerformat; fi++)
			{
			   //nowTerformat = itemSoalTerformat[fi];
			   if (itemSoalTerformat[fi].toLowerCase().indexOf("f") > -1)
			   {
				   ord = itemSoalTerformat[fi].substr(0, itemSoalTerformat[fi].length - 1);
				   soalTeracak[ord-1] = ord;
				   itemSoalTerpakai.push(itemSoalTerformat[fi]);
			   }
			}
			//#########################################################################

			//##########################################################################################################
			// ########## taruh / susun yg berformat - (ordered position = harus berurutan), dan yang lainnya ##########
			for (s=0; s<jmlItem; s++)
			{
			   if (soalTeracak[s]=='')
			   {
				   cobaComot = true;
				   while (cobaComot)
				   {
					   comotItemFormat = getRandomIntInclusive(0,nSoalTerformat-1);
					   adakah = itemSoalTerpakai.indexOf(itemSoalTerformat[comotItemFormat]);
					   if (adakah > -1) { cobaComot = true; } else { cobaComot = false; }
				   }
				   
				   if (itemSoalTerformat[comotItemFormat].indexOf("-") < 0 && itemSoalTerformat[comotItemFormat].indexOf("?") < 0)		//klo kgk ada tanda stripnya, berarti langsung angka doang
			   	   {
					   soalTeracak[s] = itemSoalTerformat[comotItemFormat];
				   }
				   else
				   {
					    if (itemSoalTerformat[comotItemFormat].indexOf("-") > -1)				//utk yg berformat - = ordered
						{
						   angkaDariKe = itemSoalTerformat[comotItemFormat].split("-");
						   itemIn = parseFloat(angkaDariKe[1])-parseFloat(angkaDariKe[0])+1;
						   
						   for (g=0; g<itemIn; g++)
						   {
							   soalTeracak[s+g] = parseFloat(angkaDariKe[0])+parseFloat(g);
						   }
						}
						else if (itemSoalTerformat[comotItemFormat].indexOf("?") > -1)				//utk yg berformat ? = unordered
						{
						   angkaDariKe = itemSoalTerformat[comotItemFormat].split("?");
						   itemIn = parseFloat(angkaDariKe[1])-parseFloat(angkaDariKe[0])+1;
						   
						   angkaDari = parseFloat(angkaDariKe[0]);
						   angkaKe = parseFloat(angkaDariKe[1]);
						   
						   getUOnumber = createUnorderedNumber(angkaDari,angkaKe);

						   for (i=0; i<itemIn; i++)
						   {
							   soalTeracak[s+i] = getUOnumber[i];
						   }
						}
				   }
				   itemSoalTerpakai.push(itemSoalTerformat[comotItemFormat]);
			   }
			}
			//#######################################################################
	}
	else
	{
		acakPerBS = itemPerBS.split("+");
		soalPerBS = susunanSoalTerformat.split("+");

		for (i=0; i<=numBS; i++)
		{
			itemSoalTerformat = soalPerBS[i].split(",");
			nSoalTerformat = itemSoalTerformat.length;			//hitung berapa banyak jadinya item soal terformat,
																//contoh : 1-2,3,4f,5-7 maka nSoalTerformat adalah 4
			formatTerpakai = 0;

			//########################################################################
			//########## taruh / susun dulu yg berformat f (fixed position) ##########
			for (fa=0; fa<nSoalTerformat; fa++)
			{
			   nowTerformat = itemSoalTerformat[fa];
			   if (nowTerformat.toLowerCase().indexOf("f") > -1)
			   {
				   ord = nowTerformat.substr(0, nowTerformat.length - 1);
				   soalTeracak[ord-1] = ord;
				   itemSoalTerpakai.push(nowTerformat);
				   formatTerpakai++;
			   }
			}

			//##########################################################################################################
			// ########## taruh / susun yg berformat - (ordered position = harus berurutan), dan yang lainnya ##########
			
			for (s=0; s<jmlItem; s++)
			{
			   if (soalTeracak[s]=='' && formatTerpakai<nSoalTerformat)
			   {
				   cobaComot = true;
				   while (cobaComot)
				   {
					   comotItemFormat = getRandomIntInclusive(0,nSoalTerformat-1);
					   adakah = itemSoalTerpakai.indexOf(itemSoalTerformat[comotItemFormat]);
					   if (adakah > -1) { cobaComot = true; } else { cobaComot = false; }
				   }
				   
				   if (itemSoalTerformat[comotItemFormat].indexOf("-") < 0 && itemSoalTerformat[comotItemFormat].indexOf("?") < 0)		//klo gak ada tanda strip atau question marknya, berarti langsung angka doang
			   	   {
					   soalTeracak[s] = itemSoalTerformat[comotItemFormat];
				   }
				   else
				   {
					    if (itemSoalTerformat[comotItemFormat].indexOf("-") > -1)
					    {
						   angkaDariKe = itemSoalTerformat[comotItemFormat].split("-");
					   	   itemIn = parseFloat(angkaDariKe[1])-parseFloat(angkaDariKe[0])+1;

						   for (g=0; g<itemIn; g++)
						   {
							   soalTeracak[s+g] = parseFloat(angkaDariKe[0])+parseFloat(g);
						   }
						}
						else if (itemSoalTerformat[comotItemFormat].indexOf("?") > -1)				//utk yg berformat ? = unordered
						{
						   angkaDariKe = itemSoalTerformat[comotItemFormat].split("?");
						   itemIn = parseFloat(angkaDariKe[1])-parseFloat(angkaDariKe[0])+1;
						   
						   angkaDari = parseFloat(angkaDariKe[0]);
						   angkaKe = parseFloat(angkaDariKe[1]);
						   
						   getUOnumber = createUnorderedNumber(angkaDari,angkaKe);

						   for (m=0; m<itemIn; m++)
						   {
							   soalTeracak[s+m] = getUOnumber[m];
						   }
						}
				   }
				   itemSoalTerpakai.push(itemSoalTerformat[comotItemFormat]);
				   formatTerpakai++;
			   }
			}
			
			//#######################################################################
		}
	}
	
	return soalTeracak.join();
}