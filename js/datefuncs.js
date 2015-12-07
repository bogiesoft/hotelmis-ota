//
// Sub days field from source date field and update the dest field
// will keep the time in the dest field format has hh:ii and time is set.
// @param source [in] form field name of src date
// @param days [in] form field name of the number of dates
// @param dest [in,out] form field name of the destination field
// @param fmt [in] text format of date field
// "mm-dd-yyyy", "mm/dd/yyyy", "dd-mm-yyyy", "dd/mm/yyyy"
// "mm-dd-yyyy hh:ii", "mm/dd/yyyy hh:ii", "dd-mm-yyyy hh:ii", "dd/mm/yyyy hh:ii"
// @return true 
//
function subDateDays(source, days,dest,fmt) {
	var mm;
	var yy;
	var dd;
	var sp = '-';
	var hh;
	var ii;
	var tt = ":";
	var Sp1;//Index of Date Separator 1
	var Sp2;//Index of Date Separator 2 
	var Sp3;// Index of the hour minute separator
	var tm = false;
	var src = document.getElementById(source).value;
	var dst = document.getElementById(dest).value;
	var tempdec = document.getElementById(days).value;
	if((fmt.toUpperCase() == 'DD/MM/YYYY') ||
	   (fmt.toUpperCase() == 'DD/MM/YYYY HH:II')) {
		var temparts = src.split(" ");
		var desttemparts = dst.split(" ");
		var parts = temparts[0].split("/");
		var tempsrc = new Date(parts[2], parts[1] - 1, parts[0]);
		if ( new Date(tempsrc) < new Date() )
		{
			 curdt=new Date();
			 dst = curdt.getDate()+"/"+curdt.getMonth()+1+"/"+curdt.getFullYear()+" "+desttemparts[1];
			// alert("Setting destination to " + dst);
			 var nextdate = new Date(curdt.getTime() + 86400000);
			 src = nextdate.getDate()+"/"+nextdate.getMonth()+1+"/"+nextdate.getFullYear()+" "+temparts[1];
			// alert ("Setting src to "+src);
			 tempdec =1;
		}
	}
	if(tempdec === undefined || tempdec == null || tempdec.length <= 0 || tempdec < 0)
		tempdec='1';
	
	var dec = parseInt(tempdec,10);
	if((fmt.toUpperCase() == 'DD/MM/YYYY') || (fmt.toUpperCase() == 'MM/DD/YYYY') || 
	   (fmt.toUpperCase() == 'DD/MM/YYYY HH:II') || (fmt.toUpperCase() == 'MM/DD/YYYY HH:II')) {
		sp = '/';
	}
	Sp1=src.indexOf(sp,0);
	Sp2=src.indexOf(sp,(parseInt(Sp1)+1));
	if((fmt.toUpperCase() == 'DD/MM/YYYY HH:II') || (fmt.toUpperCase() == 'MM/DD/YYYY HH:II') ||
	   (fmt.toUpperCase() == 'DD-MM-YYYY HH:II') || (fmt.toUpperCase() == 'MM-DD-YYYY HH:II')) {
		tm = true;
		// if the destination already has a time, keep it.
		if(dst.length > 5) {
			Sp3=dst.indexOf(tt,0);
			hh = (dst.substring(Sp3-2,Sp3));
			ii = (dst.substring(Sp3+1,Sp3+3));
		} else {
			Sp3=src.indexOf(tt,0);
			hh = (src.substring(Sp3-2,Sp3));
			ii = (src.substring(Sp3+1,Sp3+3));
		}
	}

	if((fmt.toUpperCase() == 'DD-MM-YYYY') || (fmt.toUpperCase() == 'DD/MM/YYYY') || (fmt.toUpperCase() == 'DD-MM-YYYY HH:II') || (fmt.toUpperCase() == 'DD/MM/YYYY HH:II')) {
		dd = (src.substr(0,Sp1));
		mm = (src.substring(Sp1+1,Sp2));
		yy = (src.substring(Sp2+1,Sp2+5));
	} 
	if((fmt.toUpperCase() == 'MM-DD-YYYY') || (fmt.toUpperCase() == 'MM/DD/YYYY') || (fmt.toUpperCase() == 'MM-DD-YYYY HH:II') || (fmt.toUpperCase() == 'MM/DD/YYYY HH:II')) {
		mm = (src.substr(0,Sp1));
		dd = (src.substring(Sp1+1,Sp2));
		yy = (src.substring(Sp2+1,Sp2+5));
	}

	var ndd = parseInt(dd,10);
	var nmm = parseInt(mm,10) - 1;
	var nyy = parseInt(yy,10);
//	alert(dd);
//	alert(mm);
//	alert(yy);
//	if(tm) {
//		alert(hh);
//		alert(ii);
//	}
	var sdate = new Date(nyy, nmm, ndd, 0,0,0,0);
//	alert(dec);
//	alert("start"+sdate.valueOf());
	var today = new Date();
//	alert("today "+today.valueOf());
	var msec = parseInt(sdate.valueOf(),10) - (dec * 86400000);
//	alert(msec);
	var dDate = new Date(msec);
//	alert(dDate.toString());
	dd = dDate.getDate();
	mm = dDate.getMonth()+1;//January is 0!
	yy = dDate.getFullYear();
//	alert(dd);
//	alert(mm);
//	alert(yy);
//	if(tm) {
//		alert(hh);
//		alert(ii);
//	}
	if(dd < 10) dd = '0'+dd;
	if(mm < 10) mm = '0'+mm;
	if(hh.length < 2) hh = '0'+hh;
	if(ii.length < 2) ii = '0'+ii;
	
	
	if((fmt.toUpperCase() == 'DD-MM-YYYY') || (fmt.toUpperCase() == 'DD/MM/YYYY')) {
		document.getElementById(dest).value = dd + sp + mm + sp + yy;
	} else if((fmt.toUpperCase() == 'DD-MM-YYYY HH:II') || (fmt.toUpperCase() == 'DD/MM/YYYY HH:II')) {
		document.getElementById(dest).value = dd + sp + mm + sp + yy + " " + hh + tt + ii;
	} else if((fmt.toUpperCase() == 'MM-DD-YYYY') || (fmt.toUpperCase() == 'MM/DD/YYYY')) {
		document.getElementById(dest).value = mm + sp + dd + sp + yy;
	} else {
		document.getElementById(dest).value = mm + sp + dd + sp + yy + " " + hh + tt + ii;
	}
//	alert("sub "+dec);
//	alert("sub "+src);
	document.getElementById(days).value = dec;
//	document.getElementById(source).value = src;
	
	var res = true;
	// = document.getElementById(dest).value;
	return res;
}

//
// Add source date field to the days field and update the dest field
// @param source [in] form field name of src date
// @param days [in] form field name of the number of dates
// @param dest [in,out] form field name of the destination field
// @param fmt [in] text format of date field
// "mm-dd-yyyy", "mm/dd/yyyy", "dd-mm-yyyy", "dd/mm/yyyy"
// "mm-dd-yyyy hh:ii", "mm/dd/yyyy hh:ii", "dd-mm-yyyy hh:ii", "dd/mm/yyyy hh:ii"
// @return true 
//
function addDateDays(source, days,dest,fmt) {
	var mm;
	var yy;
	var dd;
	var hh;
	var ii;
	var tt = ":";
	var sp = '-';
	var Sp1;//Index of Date Separator 1
	var Sp2;//Index of Date Separator 2 
	var Sp3;// Index of the hour minute separator
	var tm = false;

	var src = document.getElementById(source).value;
	var dst = document.getElementById(dest).value;
	if((fmt.toUpperCase() == 'DD/MM/YYYY') ||
	   (fmt.toUpperCase() == 'DD/MM/YYYY HH:II')) {
		var temparts = src.split(" ");
		var parts = temparts[0].split("/");
		var tempsrc = new Date(parts[2], parts[1] - 1, parts[0]);
		if ( new Date(tempsrc) < new Date() )
		{
			curdt=new Date();
			var nmonth = curdt.getMonth()+1;
			var src = curdt.getDate()+"/"+nmonth+"/"+curdt.getFullYear()+" "+temparts[1];
			//alert("Setting source to" + src);
		}
	}
	
	var tempinc = document.getElementById(days).value;
	if(tempinc === undefined || tempinc == null || tempinc.length <= 0 || tempinc < 0)
		tempinc='1';
	var inc = parseInt(tempinc,10);
	if((fmt.toUpperCase() == 'DD/MM/YYYY') || (fmt.toUpperCase() == 'MM/DD/YYYY')  || 
	   (fmt.toUpperCase() == 'DD/MM/YYYY HH:II') || (fmt.toUpperCase() == 'MM/DD/YYYY HH:II')) {
		sp = '/';
	}
	Sp1=src.indexOf(sp,0);
	Sp2=src.indexOf(sp,(parseInt(Sp1)+1));
	if((fmt.toUpperCase() == 'DD/MM/YYYY HH:II') || (fmt.toUpperCase() == 'MM/DD/YYYY HH:II') ||
	   (fmt.toUpperCase() == 'DD-MM-YYYY HH:II') || (fmt.toUpperCase() == 'MM-DD-YYYY HH:II')) {
		tm = true;
		// if the destination already has a time, keep it.
		if(dst.length > 5) {
			Sp3=dst.indexOf(tt,0);
			hh = (dst.substring(Sp3-2,Sp3));
			ii = (dst.substring(Sp3+1,Sp3+3));
		} else {
			Sp3=src.indexOf(tt,0);
			hh = (src.substring(Sp3-2,Sp3));
			ii = (src.substring(Sp3+1,Sp3+3));
		}
	}

	if((fmt.toUpperCase() == 'DD-MM-YYYY') || (fmt.toUpperCase() == 'DD/MM/YYYY') || (fmt.toUpperCase() == 'DD-MM-YYYY HH:II') || (fmt.toUpperCase() == 'DD/MM/YYYY HH:II')) {
		dd = (src.substr(0,Sp1));
		mm = (src.substring(Sp1+1,Sp2));
		yy = (src.substring(Sp2+1,Sp2+5));
	} 
	if((fmt.toUpperCase() == 'MM-DD-YYYY') || (fmt.toUpperCase() == 'MM/DD/YYYY') || (fmt.toUpperCase() == 'MM-DD-YYYY HH:II') || (fmt.toUpperCase() == 'MM/DD/YYYY HH:II')) {
		mm = (src.substr(0,Sp1));
		dd = (src.substring(Sp1+1,Sp2));
		yy = (src.substring(Sp2+1,Sp2+5));
	}
	var ndd = parseInt(dd,10);
	var nmm = parseInt(mm,10) - 1;
	var nyy = parseInt(yy,10);
//	alert(dd);
//	alert(mm);
//	alert(yy);
//	if(tm) {
//		alert(hh);
//		alert(ii);
//	}
	
	var sdate = new Date(nyy, nmm, ndd, 0,0,0,0);
//	alert(inc);
//	alert("start"+sdate.valueOf());
	var today = new Date();
//	alert("today "+today.valueOf());
	var msec = parseInt(sdate.valueOf(),10) + (inc * 86400000);
//	alert(msec);
	var dDate = new Date(msec);
//	alert(dDate.toString());
	dd = dDate.getDate();
	mm = dDate.getMonth()+1;//January is 0!
	yy = dDate.getFullYear();
//	alert(dd);
//	alert(mm);
//	alert(yy);
//	if(tm) {
//		alert(hh);
//		alert(ii);
//	}
	if(dd/1 < 10) dd = '0'+dd;
	if(mm/1 < 10) mm = '0'+mm;
	if(hh.length < 2) hh = '0'+hh;
	if(ii.length < 2) ii = '0'+ii;

	
	if((fmt.toUpperCase() == 'DD-MM-YYYY') || (fmt.toUpperCase() == 'DD/MM/YYYY')) {
		document.getElementById(dest).value = dd + sp + mm + sp + yy;
	} else if((fmt.toUpperCase() == 'DD-MM-YYYY HH:II') || (fmt.toUpperCase() == 'DD/MM/YYYY HH:II')) {
		document.getElementById(dest).value = dd + sp + mm + sp + yy + " " + hh + tt + ii;
	} else if((fmt.toUpperCase() == 'MM-DD-YYYY') || (fmt.toUpperCase() == 'MM/DD/YYYY')) {
		document.getElementById(dest).value = mm + sp + dd + sp + yy;
	} else {
		document.getElementById(dest).value = mm + sp + dd + sp + yy + " " + hh + tt + ii;
	}
	document.getElementById(days).value = inc;
//	document.getElementById(source).value = src;
	var res = true;
	// = document.getElementById(dest).value;
	return res;
}


//
// Return the number of days between 2 dates 2nd date - 1st date = days.
// @param date1 [in] form field name of 1st date
// @param date2 [in] form field name of 2nd date
// @param dest [in,out] form field name of the destination field
// @param fmt [in] text format of date field
// "mm-dd-yyyy", "mm/dd/yyyy", "dd-mm-yyyy", "dd/mm/yyyy"
// "mm-dd-yyyy hh:ii", "mm/dd/yyyy hh:ii", "dd-mm-yyyy hh:ii", "dd/mm/yyyy hh:ii"
// @return true 
//
function subDates(date1, date2,dest,fmt) {
	var mm;
	var yy;
	var dd;
	var sp = '-';
	var hh;
	var ii;
	var res = true;
	var tt = ":";
	var Sp1;//Index of Date Separator 1
	var Sp2;//Index of Date Separator 2 
	var Sp3;// Index of the hour minute separator
	var tm = false;
//	alert("1");
	var src = document.getElementById(date1).value;
	var dst = document.getElementById(date2).value;
	// No dates so nothing to subtract
	if(dst.length < 5 || src.length < 5 ) {
		return res;
	}
	// check the format
	if((fmt.toUpperCase() == 'DD/MM/YYYY') || (fmt.toUpperCase() == 'MM/DD/YYYY') || 
	   (fmt.toUpperCase() == 'DD/MM/YYYY HH:II') || (fmt.toUpperCase() == 'MM/DD/YYYY HH:II')) {
		sp = '/';
	}
	Sp1=src.indexOf(sp,0);
	Sp2=src.indexOf(sp,(parseInt(Sp1)+1));
	if((fmt.toUpperCase() == 'DD/MM/YYYY HH:II') || (fmt.toUpperCase() == 'MM/DD/YYYY HH:II') ||
	   (fmt.toUpperCase() == 'DD-MM-YYYY HH:II') || (fmt.toUpperCase() == 'MM-DD-YYYY HH:II')) {
			tm = true;
	}
	

	if((fmt.toUpperCase() == 'DD-MM-YYYY') || (fmt.toUpperCase() == 'DD/MM/YYYY') || (fmt.toUpperCase() == 'DD-MM-YYYY HH:II') || (fmt.toUpperCase() == 'DD/MM/YYYY HH:II')) {
		dd = (src.substr(0,Sp1));
		mm = (src.substring(Sp1+1,Sp2));
		yy = (src.substring(Sp2+1,Sp2+5));
	} 
	if((fmt.toUpperCase() == 'MM-DD-YYYY') || (fmt.toUpperCase() == 'MM/DD/YYYY') || (fmt.toUpperCase() == 'MM-DD-YYYY HH:II') || (fmt.toUpperCase() == 'MM/DD/YYYY HH:II')) {
		mm = (src.substr(0,Sp1));
		dd = (src.substring(Sp1+1,Sp2));
		yy = (src.substring(Sp2+1,Sp2+5));
	}

	var ndd = parseInt(dd,10);
	var nmm = parseInt(mm,10) - 1;
	var nyy = parseInt(yy,10);
	// get the start date - date 1.
	var sdate = new Date(nyy, nmm, ndd, 0,0,0,0);
//	alert(sdate);
	
	
	Sp1=dst.indexOf(sp,0);
	Sp2=dst.indexOf(sp,(parseInt(Sp1)+1));
	if((fmt.toUpperCase() == 'DD/MM/YYYY HH:II') || (fmt.toUpperCase() == 'MM/DD/YYYY HH:II') ||
	   (fmt.toUpperCase() == 'DD-MM-YYYY HH:II') || (fmt.toUpperCase() == 'MM-DD-YYYY HH:II')) {
		tm = true;
	}

	if((fmt.toUpperCase() == 'DD-MM-YYYY') || (fmt.toUpperCase() == 'DD/MM/YYYY') || (fmt.toUpperCase() == 'DD-MM-YYYY HH:II') || (fmt.toUpperCase() == 'DD/MM/YYYY HH:II')) {
		dd = (dst.substr(0,Sp1));
		mm = (dst.substring(Sp1+1,Sp2));
		yy = (dst.substring(Sp2+1,Sp2+5));
	} 
	if((fmt.toUpperCase() == 'MM-DD-YYYY') || (fmt.toUpperCase() == 'MM/DD/YYYY') || (fmt.toUpperCase() == 'MM-DD-YYYY HH:II') || (fmt.toUpperCase() == 'MM/DD/YYYY HH:II')) {
		mm = (dst.substr(0,Sp1));
		dd = (dst.substring(Sp1+1,Sp2));
		yy = (dst.substring(Sp2+1,Sp2+5));
	}

	ndd = parseInt(dd,10);
	nmm = parseInt(mm,10) - 1;
	nyy = parseInt(yy,10);
	var edate = new Date(nyy, nmm, ndd, 0,0,0,0);
	
	var one_day=1000*60*60*24;
	var ndays = Math.ceil((edate.getTime()-sdate.getTime())/(one_day));
	if (ndays < 0) {
		ndays = 0 - ndays;
	}
	document.getElementById(dest).value = ndays;

	return res;
}
