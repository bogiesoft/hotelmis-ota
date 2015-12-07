// Original:  Simon Tneoh (tneohcb@pc.jaring.my)
// This script and many more are available free online at -->
//The JavaScript Source!! http://javascript.internet.com -->

var Cards = new makeArray(8);
Cards[0] = new CardType("CA", "51,52,53,54,55", "16");
var CA = Cards[0];
Cards[1] = new CardType("VI", "4", "13,16");
var VI = Cards[1];
Cards[2] = new CardType("AX", "34,37", "15");
var AX = Cards[2];
Cards[3] = new CardType("DC", "30,36,38", "14");
var DC = Cards[3];
Cards[4] = new CardType("DI", "6011", "16");
var DI = Cards[4];
Cards[5] = new CardType("ER", "2014,2149", "15");
var ER = Cards[5];
Cards[6] = new CardType("JCB", "3088,3096,3112,3158,3337,3528", "16");
var JCB = Cards[6];
var LuhnCheckSum = Cards[7] = new CardType();

/*************************************************************************\
function called when users change the credit card
@param cardtype [in] field id for the card type selection
@param CC [in] field id for the credit card number
@param exp [in] field id for the credit card expiry in MMYY
modified to use the field id names for the CC number and date
Neil Thyer e-Novate 2011 http://www.e-novate.asia
\*************************************************************************/
function CheckCardNumber(ctype,CCn,exp) {
	var tmpmon;
	var tmpyear;
	var mm;
	var yy;
	mmyy = document.getElementById(exp).value;
	if(mmyy.length == 0) return;
	
	tmpmon = mmyy.substr(0,2);
	tmpyear = mmyy.substr(2,2);
//	alert(tmpmon + " " + tmpyear);
	tmpyear = "20" + tmpyear;
	var ccno = document.getElementById(CCn);
	ccno.value = ccno.value.replace(/ /g,"");
	ccno.value = ccno.value.replace(/-/g,"");
	var dDate = new Date();
//	alert(dDate.toString());
	mm = dDate.getMonth()+1;//January is 0!
	yy = dDate.getFullYear();
	
	if (!(new CardType()).isExpiryDate(tmpyear, tmpmon)) {
		alert("This card has already expired.");
		return;
	}
	card = document.getElementById(ctype).value;
	var retval = eval(card + ".checkCardNumber(\"" + ccno.value +
	"\", " + tmpyear + ", " + tmpmon + ");");
//	alert(card + ".checkCardNumber(\"" + ccno.value +
//	"\", " + tmpyear + ", " + tmpmon + ");")
	cardname = "";
	if (retval)
		// comment this out if used on an order form
		alert("This card number appears to be valid.");
	else {
		// The cardnumber has the valid luhn checksum, but we want to know which
		// cardtype it belongs to.
		for (var n = 0; n < Cards.size; n++) {
			if (Cards[n].checkCardNumber(ccno.value, tmpyear, tmpmon)) {
				cardname = Cards[n].getCardType();
				break;
		    }
		}
		if (cardname.length > 0) {
			alert("This looks like a " + cardname + " number, not a " + card + " number.");
		} else {
			alert("This card number is not valid.");
		}
	}
}
/*************************************************************************\
Object CardType([String cardtype, String rules, String len, int year, 
                                        int month])
cardtype    : type of card, eg: MasterCard, Visa, etc.
rules       : rules of the cardnumber, eg: "4", "6011", "34,37".
len         : valid length of cardnumber, eg: "16,19", "13,16".
year        : year of expiry date.
month       : month of expiry date.
eg:
var VisaCard = new CardType("VI", "4", "16");
var AmExCard = new CardType("AX", "34,37", "15");
\*************************************************************************/
function CardType() {
	var n;
	var argv = CardType.arguments;
	var argc = CardType.arguments.length;

	this.objname = "object CardType";

	var tmpcardtype = (argc > 0) ? argv[0] : "CardObject";
	var tmprules = (argc > 1) ? argv[1] : "0,1,2,3,4,5,6,7,8,9";
	var tmplen = (argc > 2) ? argv[2] : "13,14,15,16,19";

	this.setCardNumber = setCardNumber;  // set CardNumber method.
	this.setCardType = setCardType;  // setCardType method.
	this.setLen = setLen;  // setLen method.
	this.setRules = setRules;  // setRules method.
	this.setExpiryDate = setExpiryDate;  // setExpiryDate method.

	this.setCardType(tmpcardtype);
	this.setLen(tmplen);
	this.setRules(tmprules);
	if (argc > 4)
	this.setExpiryDate(argv[3], argv[4]);

	this.checkCardNumber = checkCardNumber;  // checkCardNumber method.
	this.getExpiryDate = getExpiryDate;  // getExpiryDate method.
	this.getCardType = getCardType;  // getCardType method.
	this.isCardNumber = isCardNumber;  // isCardNumber method.
	this.isExpiryDate = isExpiryDate;  // isExpiryDate method.
	this.luhnCheck = luhnCheck;// luhnCheck method.
	return this;
}

/*************************************************************************\
boolean checkCardNumber([String cardnumber, int year, int month])
return true if cardnumber pass the luhncheck and the expiry date is
valid, else return false.
\*************************************************************************/
function checkCardNumber() {
	var argv = checkCardNumber.arguments;
	var argc = checkCardNumber.arguments.length;
	var cardnumber = (argc > 0) ? argv[0] : this.cardnumber;
	var year = (argc > 1) ? argv[1] : this.year;
	var month = (argc > 2) ? argv[2] : this.month;

	this.setCardNumber(cardnumber);
	this.setExpiryDate(year, month);

	if (!this.isCardNumber())
		return false;
	if (!this.isExpiryDate())
		return false;

	return true;
}
/*************************************************************************\
String getCardType()
return the cardtype.
\*************************************************************************/
function getCardType() {
	return this.cardtype;
}
/*************************************************************************\
String getExpiryDate()
return the expiry date.
\*************************************************************************/
function getExpiryDate() {
	return this.month + "/" + this.year;
}
/*************************************************************************\
boolean isCardNumber([String cardnumber])
return true if cardnumber pass the luhncheck and the rules, else return
false.
\*************************************************************************/
function isCardNumber() {
	var argv = isCardNumber.arguments;
	var argc = isCardNumber.arguments.length;
	var cardnumber = (argc > 0) ? argv[0] : this.cardnumber;
	if (!this.luhnCheck())
		return false;

	for (var n = 0; n < this.len.size; n++)
		if (cardnumber.toString().length == this.len[n]) {
			for (var m = 0; m < this.rules.size; m++) {
				var headdigit = cardnumber.substring(0, this.rules[m].toString().length);
				if (headdigit == this.rules[m])
					return true;
			}
			return false;
		}
	return false;
}

/*************************************************************************\
boolean isExpiryDate([int year, int month])
return true if the date is a valid expiry date,
else return false.
\*************************************************************************/
function isExpiryDate() {
	var argv = isExpiryDate.arguments;
	var argc = isExpiryDate.arguments.length;

	year = argc > 0 ? argv[0] : this.year;
	month = argc > 1 ? argv[1] : this.month;

	if (!isNum(year+""))
		return false;
	if (!isNum(month+""))
		return false;
	today = new Date();
	expiry = new Date(year, month);
	if (today.getTime() > expiry.getTime())
		return false;
	else
		return true;
}

/*************************************************************************\
boolean isNum(String argvalue)
return true if argvalue contains only numeric characters,
else return false.
\*************************************************************************/
function isNum(argvalue) {
	argvalue = argvalue + '';

	if (argvalue.length == 0)
	return false;

	for (var n = 0; n < argvalue.length; n++)
		if (argvalue.substring(n, n+1) < "0" || argvalue.substring(n, n+1) > "9")
			return false;

	return true;
}

/*************************************************************************\
boolean luhnCheck([String CardNumber])
return true if CardNumber pass the luhn check else return false.
Reference: http://www.ling.nwu.edu/~sburke/pub/luhn_lib.pl
\*************************************************************************/
function luhnCheck() {
	var argv = luhnCheck.arguments;
	var argc = luhnCheck.arguments.length;

	var CardNumber = argc > 0 ? argv[0] : this.cardnumber;

	if (! isNum(CardNumber)) {
		return false;
	}

	var no_digit = CardNumber.length;
	var oddoeven = no_digit & 1;
	var sum = 0;

	for (var count = 0; count < no_digit; count++) {
		var digit = parseInt(CardNumber.charAt(count));
		if (!((count & 1) ^ oddoeven)) {
			digit *= 2;
			if (digit > 9)
			digit -= 9;
		}
		sum += digit;
	}
	if (sum % 10 == 0)
		return true;
	else
		return false;
}

/*************************************************************************\
ArrayObject makeArray(int size)
return the array object in the size specified.
\*************************************************************************/
function makeArray(size) {
	this.size = size;
	return this;
}

/*************************************************************************\
CardType setCardNumber(cardnumber)
return the CardType object.
\*************************************************************************/
function setCardNumber(cardnumber) {
	this.cardnumber = cardnumber;
	return this;
}

/*************************************************************************\
CardType setCardType(cardtype)
return the CardType object.
\*************************************************************************/
function setCardType(cardtype) {
	this.cardtype = cardtype;
	return this;
}

/*************************************************************************\
CardType setExpiryDate(year, month)
return the CardType object.
\*************************************************************************/
function setExpiryDate(year, month) {
	this.year = year;
	this.month = month;
	return this;
}

/*************************************************************************\
CardType setLen(len)
return the CardType object.
\*************************************************************************/
function setLen(len) {
	// Create the len array.
	if (len.length == 0 || len == null)
	len = "13,14,15,16,19";

	var tmplen = len;
	n = 1;
	while (tmplen.indexOf(",") != -1) {
		tmplen = tmplen.substring(tmplen.indexOf(",") + 1, tmplen.length);
		n++;
	}
	this.len = new makeArray(n);
	n = 0;
	while (len.indexOf(",") != -1) {
		var tmpstr = len.substring(0, len.indexOf(","));
		this.len[n] = tmpstr;
		len = len.substring(len.indexOf(",") + 1, len.length);
		n++;
	}
	this.len[n] = len;
	return this;
}

/*************************************************************************\
CardType setRules()
return the CardType object.
\*************************************************************************/
function setRules(rules) {
	// Create the rules array.
	if (rules.length == 0 || rules == null)
	rules = "0,1,2,3,4,5,6,7,8,9";
	  
	var tmprules = rules;
	n = 1;
	while (tmprules.indexOf(",") != -1) {
		tmprules = tmprules.substring(tmprules.indexOf(",") + 1, tmprules.length);
		n++;
	}
	this.rules = new makeArray(n);
	n = 0;
	while (rules.indexOf(",") != -1) {
		var tmpstr = rules.substring(0, rules.indexOf(","));
		this.rules[n] = tmpstr;
		rules = rules.substring(rules.indexOf(",") + 1, rules.length);
		n++;
	}
	this.rules[n] = rules;
	return this;
}
