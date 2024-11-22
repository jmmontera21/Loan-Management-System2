function showNotification(title, details) {
    document.getElementById('notification-title').innerText = title;
    document.getElementById('notification-details').innerText = details;
    document.getElementById('notification-popup').style.display = 'block';
}

function closeNotification() {
    document.getElementById('notification-popup').style.display = 'none';
}

function updateCarousel() {
    const carouselInner = document.querySelector('.carousel-inner');
    carouselInner.style.transform = `translateX(-${currentSlide * 100}%)`;
    document.getElementById('notification-popup').style.display = 'none';  // Hide the notification pop-up on slide change
}


/*Employee Page*/
function showNotifications() {
    alert("You have new notifications!");
}

/*Employee Customers*/
$(document).ready(function() {
    $('#customersTable').DataTable();
});

function toggleSidebar() {
    var sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('minimized');
    var toggleIcon = document.querySelector('.toggle-icon');
    toggleIcon.innerHTML = sidebar.classList.contains('minimized') ? '>' : '<';
}

function toggleDropdown() {
    var dropdownContent = document.querySelector('.user .dropdown-content');
    dropdownContent.classList.toggle('show');
}

window.onclick = function(event) {
    if (!event.target.matches('.user') && !event.target.matches('.dropdown-icon')) {
        var dropdowns = document.getElementsByClassName('dropdown-content');
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

function showAddCustomerForm() {
    var modal = document.getElementById('addCustomerModal');
    modal.style.display = "block";
}

window.onclick = function(event) {
    var modal = document.getElementById('addCustomerModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function closeAddCustomerForm() {
    document.getElementById('addCustomerModal').style.display = 'none';
}

function validateForm() {
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;

    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert('Please enter a valid email address.');
        return false;
    }     
    
    if (password.length < 6) {
        alert('Password must be at least 6 characters long.');
        return false;
    }

    return true;
}

function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('imagePreview');
        output.innerHTML = '<img src="' + reader.result + '" alt="Image Preview">';
    }
    reader.readAsDataURL(event.target.files[0]);
}

function showAlert(message) {
    var alertBox = document.getElementById('alert');
    alertBox.innerHTML = message;
    alertBox.className = "alert show";
    setTimeout(function() {
        alertBox.className = alertBox.className.replace("show", "");
    }, 3000);
}

function updateBarangays() {
    var barangaySelect = document.getElementById('comaker_barangay');
    var citySelect = document.getElementById('comaker_municipality');
    var selectedCity = citySelect.value;
    var barangays = {
        "Agdangan": ["Binagbag", "Dayap", "Ibabang Kinagunan", "Ilayang Kinagunan", 
                    "Kanlurang Calutan", "Kanlurang Maligaya", "Poblacion I", "Poblacion II", "Salvacion",
                    "Silangang Calutan", "Silangang Maligaya", "Sildora"],
        "Alabat": ["Angeles", "Bacong", "Balungay", "Barangay 1", "Barangay 2",
                    "Barangay 3", "Barangay 4", "Barangay 5", "Buenavista", "Caglate", "Camagong", "Gordon",
                    "Pambilan Norte", "Pambilan Sur", "Villa Esperanza", "Villa Jesus Este", "Villa Jesus Weste",
                    "Villa Norte", "Villa Victoria"],
        "Atimonan": ["Angeles", "Balubad", "Balugohin", "Barangay Zone 1", "Barangay Zone 2", "Barangay Zone 3",
                    "Barangay Zone 4", "Buhangin", "Caridad Ibaba", "Caridad Ilaya", "Habingan", "Inaclagan", "Inalig",
                    "Kilait", "Kulawit", "Lakip", "Lubi", "Lumutan", "Magsaysay", "Malinao Ibaba", "Malinao Ilaya",
                    "Malusak", "Manggalayan Bundok", "Manggalayan Labak", "Matanag", "Montes Balaon", "Montes Kallagan",
                    "Ponon", "Rizal", "San Andres Bundok", "San Andres Labak", "San Isidro", "San Jose Balatok", "San Rafael",
                    "Santa Catalina", "Sapaan", "Sokol", "Tagbakin", "Talaba", "Tinandog", "Villa Ibaba", "Villa Ilaya"],
        "Buenavista": ["Bagong Silang", "Batabat Norte", "Batabat Sur", "Buenavista", "Bukal", "Bulo", "Cabong", "Cadlit",
                    "Catulin", "Cawa", "De La Paz", "Del Rosario", "Hagonghong", "Ibabang Wasay", "Ilayang Wasay", "Lilukin",
                    "Mabini", "Mabutag", "Magallanes", "Maligaya", "Manlana", "Masaya", "Poblacion", "Rizal", "Sabang Pinamasagan", 
                    "Sabang Piris", "San Diego", "San Isidro Ibaba", "San Isidro Ilaya", "San Pablo", "San Pedro", "San Vicente",
                    "Siain", "Villa Aurora", "Villa Batabat", "Villa Magsaysay", "Villa Veronica"],
        "Burdeos": ["Aluyon", "Amot", "Anibawan", "Bonifacio", "Cabugao", "Cabungalunan", "Calutcot", "Caniwan", "Carlagan",
                    "Mabini", "Palasan", "Poblacion", "San Rafael"],
        "Calauag": ["Agoho", "Anahawan", "Anas", "Apad Lutao", "Apad Quezon", "Apad Taisan", "Atulayan", "Baclaran", "Bagong Silang", 
                    "Balibago", "Bangkuruhan", "Bantolinao", "Barangay I", "Barangay II", "Barangay III", "Barangay IV", "Barangay V", 
                    "Bigaan", "Binutas", "Biyan", "Bukal", "Buli", "Dapdap", "Dominlog", "Doña Aurora", "Guinosayan", "Ipil", "Kalibo",
                    "Kapaluhan", "Katangtang", "Kigtan", "Kinalin Ibaba", "Kinalin Ilaya", "Kinamaligan", "Kumaludkud", "Kunalum", "Kuyaoyao",
                    "Lagay", "Lainglaingan", "Lungib", "Mabini", "Madlangdungan", "Maglipad", "Maligaya", "Mambaling", "Manhulugin", "Marilag", "Mulay", "Pandanan", "Pansol", "Patihan",
                    "Pinagbayanan", "Pinagkamaligan", "Pinagsakahan", "Pinagtalleran", "Rizal Ibaba", "Rizal Ilaya", "Sabang I", "Sabang II", "Salvacion", 
                    "San Quintin", "San Roque Ibaba", "San Roque Ilaya", "Santa Cecilia", "Santa Maria", "Santa Milagrosa", "Santa Rosa", "Santo Angel",
                    "Santo Domingo", "Sinag", "Sumilang", "Sumulong", "Tabansak", "Talingting", "Tamis", "Tikiwan", "Tiniguiban", "Villa Magsino", 
                    "Villa San Isidro", "Viñas", "Yaganak"],
        "Candelaria": ["Buenavista East", "Buenavista West", "Bukal Norte", "Bukal Sur", "Kinatihan I", "Kinatihan II", "Malabanban Norte", "Malabanban Sur",
                    "Mangilag Norte", "Mangilag Sur", "Masalukot I", "Masalukot II", "Masalukot III", "Masalukot IV", "Masalukot V", "Masin Norte", "Masin Sur", 
                    "Mayabobo", "Pahinga Norte", "Pahinga Sur", "Poblacion", "San Andres", "San Isidro", "Santa Catalina Norte", "Santa Catalina Sur"],
        "Catanauan": ["Ajos", "Anusan", "Barangay 1", "Barangay 2", "Barangay 3", "Barangay 4", "Barangay 5", "Barangay 6", "Barangay 7", "Barangay 8", "Barangay 9",
                    "Barangay 10", "Bolo", "Bulagsong", "Camandiison", "Canculajao", "Catumbo", "Cawayanin Ibaba", "Cawayanin Ilaya",
                    "Cutcutan", "Dahican", "Doongan Ibaba", "Doongan Ilaya", "Gatasan", "Macpac", "Madulao", "Matandang Sabang Kanluran", "Matandang Sabang Silangan",
                    "Milagrosa", "Navitas", "Pacabit", "San Antonio Magkupa", "San Antonio Pala", "San Isidro", "San Jose", "San Pablo", "San Roque", "San Vicente Kanluran",
                    "San Vicente Silangan", "Santa Maria", "Tagabas Ibaba", "Tagabas Ilaya", "Tagbacan Ibaba", "Tagbacan Ilaya", "Tagbacan Silangan", "Tuhian"],
        "Dolores": ["Antonino", "Bagong Anyo", "Bayanihan", "Bulakin I", "Bulakin II", "Bungoy", "Cabatang", "Dagatan", "Kinabuhayan", "Maligaya", "Manggahan", "Pinagdanlayan",
                    "Putol", "San Mateo", "Santa Lucia", "Silanganan"],
        "General Luna": ["Bacong Ibaba", "Bacong Ilaya", "Barangay 1", "Barangay 2", "Barangay 3", "Barangay 4", "Barangay 5", "Barangay 6", "Barangay 7", "Barangay 8",
                    "Barangay 9", "Lavides", "Magsaysay", "Malaya", "Nieva", "Recto", "San Ignacio Ibaba", "San Ignacio Ilaya", "San Isidro Ibaba", "San Isidro Ilaya", "San Jose", 
                    "San Nicolas", "San Vicente", "Santa Maria Ibaba", "Santa Maria Ilaya", "Sumilang", "Villarica"],
        "General Nakar": ["Anoling", "Banglos", "Batangan", "Canaway", "Catablingan", "Lumutan", "Magsikap", "Mahabang Lalim", "Maigang", "Maligaya",
                    "Minahan Norte", "Minahan Sur", "Pagsangahan", "Pamplona", "Pisa", "Poblacion", "Sablang", "San Marcelino", "Umiray"],
        "Guinayangan": ["A. Mabini", "Aloneros", "Arbismen", "Bagong Silang", "Balinarin", "Bukal Maligaya", "Cabibihan", "Cabong Norte", "Cabong Sur", "Calimpak", "Capuluan Central",
                    "Capuluan Tulon", "Dancalan Caimawan", "Dancalan Central", "Danlagan Batis", "Danlagan Cabayao", "Danlagan Central", "Danlagan Reserva", "Del Rosario",
                    "Dungawan Central", "Dungawan Paalyunan", "Dungawan Pantay", "Ermita", "Gapas", "Himbubulo Este", "Himbubulo Weste", "Hinabaan", "Ligpit Bantayan", "Lubigan",
                    "Magallanes", "Magsaysay", "Manggagawa", "Manggalang", "Manlayo", "Poblacion", "Salakan", "San Antonio", "San Isidro", "San Jose", "San Lorenzo", "San Luis I",
                    "San Luis II", "San Miguel", "San Pedro I", "San Pedro II", "San Roque", "Santa Cruz", "Santa Maria", "Santa Teresita", "Sintones", "Sisi", "Tikay", "Triumpo", "Villa Hiwasayan"],
        "Gumaca": ["Adia Bitaog", "Anonangin", "Bagong Buhay", "Bamban", "Bantad", "Batong Dalig", "Biga", "Binambang", "Buensuceso", "Bungahan", "Butaguin", "Calumangin", "Camohaguin",
                    "Casasahan Ibaba", "Casasahan Ilaya", "Cawayan", "Gayagayaan", "Gitnang Barrio", "Hagakhakin", "Hardinan", "Inaclagan", "Inagbuhan Ilaya", "Labnig", "Laguna", "Lagyo", "Mabini",
                    "Mabunga", "Malabtog", "Manlayaan", "Marcelo H. del Pilar", "Mataas na Bundok", "Maunlad", "Pagsabangan", "Panikihan", "Peñafrancia", "Pipisik", "Progreso", "Rizal",
                    "Rosario", "San Agustin", "San Diego", "San Diego Poblacion", "San Isidro Kanluran", "San Isidro Silangan", "San Juan de Jesus", "San Vicente", "Sastre", "Tabing Dagat", "Tumayan",
                    "Villa Arcaya", "Villa Bota", "Villa Fuerte", "Villa M. Principe", "Villa Mendoza", "Villa Nava", "Villa Padua", "Villa Perez", "Villa Tañada", "Villa Victoria"],
        "Infanta": ["Abiawin", "Agos-agos", "Alitas", "Amolongin", "Anibong", "Antikin", "Bacong", "Balobo", "Bantilan", "Banugao", "Batican", "Binonoan", "Binulasan", "Boboin", "Catambungan", "Cawaynin",
                    "Comon", "Dinahican", "Gumian", "Ilog", "Ingas", "Langgas", "Libjo", "Lual", "Magsaysay", "Maypulot", "Miswa", "Pilaway", "Pinaglapatan", "Poblacion 1", "Poblacion 38", "Poblacion 39",
                    "Pulo", "Silangan", "Tongohin", "Tudturan"],
        "Jomalig": ["Apad", "Bukal", "Casuguran", "Gango", "Talisoy"],
        "Lopez": ["Bacungan", "Bagacay", "Banabahin Ibaba", "Banabahin Ilaya", "Bayabas", "Bebito", "Bigajo", "Binahian A", "Binahian B", "Binahian C", "Bocboc", "Buenavista", "Burgos", "Buyacanin", "Cagacag", 
                    "Calantipayan", "Canda Ibaba", "Canda Ilaya", "Cawayan", "Cawayanin", "Cogorin Ibaba", "Cogorin Ilaya", "Concepcion", "Danlagan", "De La Paz", "Del Pilar", "Del Rosario", "Esperanza Ibaba",
                    "Esperanza Ilaya", "Gomez", "Guihay", "Guinuangan", "Guites", "Hondagua", "Ilayang Ilog A", "Ilayang Ilog B", "Inalusan", "Jongo", "Lalaguna", "Lourdes", "Mabanban", "Mabini", "Magallanes",
                    "Magsaysay", "Maguilayan", "Mahayod-Hayod", "Mal-ay", "Mandoog", "Manguisian", "Matinik", "Monteclaro", "Pamampangin", "Pansol", "Peñafrancia", "Pisipis", "Rizal (Rural)", "Rizal (Poblacion)",
                    "Roma", "Rosario", "Samat", "San Andres", "San Antonio", "San Francisco A", "San Francisco B", "San Isidro", "San Jose", "San Miguel", "San Pedro", "San Rafael", "San Roque", "Santa Catalina",
                    "Santa Elena", "Santa Jacobe", "Santa Lucia", "Santa Maria", "Santa Rosa", "Santa Teresa", "Santo Niño Ibaba", "Santo Niño Ilaya", "Silang", "Sugod", "Sumalang", "Talolong", "Tan-ag Ibaba",
                    "Tan-ag Ilaya", "Tocalin", "Vegaflor", "Vergaña", "Veronica", "Villa Aurora", "Villa Espina", "Villa Geda", "Villa Hermosa", "Villamonte", "Villanacaob"],
        "Lucban": ["Abang", "Aliliw", "Atulinao", "Ayuti", "Baranggay I", "Baranggay II", "Baranggay III", "Baranggay IV", "Baranggay V", "Baranggay VI", "Baranggay VII", "Baranggay VIII", "Baranggay IX", "Baranggay X",
                    "Igang", "Kabatete", "Kakawit", "Kalangay", "Kalyaat", "Kilib", "Kulapi", "Mahabang Parang", "Malupak", "Manasa", "May-it", "Nagsinamo", "Nalunao", "Palola", "Piis", "Samil", "Tiawe", "Tinamnan"],
        "Lucena City": ["Barangay 1", "Barangay 2", "Barangay 3", "Barangay 4", "Barangay 5", "Barangay 6", "Barangay 7", "Barangay 8", "Barangay 9", "Barangay 10", "Barangay 11", "Barra", "Bocohan", "Cotta", "Dalahican",
                    "Domoit", "Gulang-gulang", "Ibabang Dupay", "Ibabang Iyam", "Ibabang Talim", "Ilayang Dupay", "Ilayang Iyam", "Ilayang Talim", "Isabang", "Market View", "Mayao Castillo", "Mayao Crossing", "Mayao Kanluran",
                    "Mayao Parada", "Mayao Silangan", "Ransohan", "Salinas", "Talao-talao"],
        "Macalelon": ["Amontay", "Anos", "Buyao", "Calantas", "Candangal", "Castillo", "Damayan", "Lahing", "Luctob", "Mabini Ibaba", "Mabini Ilaya", "Malabahay", "Mambog", "Masipag", "Olongtao Ibaba", "Olongtao Ilaya",
                    "Padre Herrera", "Pag-asa", "Pajarillo", "Pinagbayanan", "Rizal", "Rodriquez", "San Isidro", "San Jose", "San Nicolas", "San Vicente", "Taguin", "Tubigan Ibaba", "Tubigan Ilaya", "Vista Hermosa"],
        "Mauban": ["Abo-abo", "Alitap", "Baao", "Bagong Bayan", "Balaybalay", "Bato", "Cagbalete I", "Cagbalete II", "Cagsiay I", "Cagsiay II", "Cagsiay III", "Concepcion", "Daungan", "Liwayway", "Lual", "Lual Rural",
                    "Lucutan", "Luya-luya", "Mabato", "Macasin", "Polo", "Remedios I", "Remedios II", "Rizaliana", "Rosario", "Sadsaran", "San Gabriel", "San Isidro", "San Jose", "San Lorenzo", "San Miguel", "San Rafael",
                    "San Roque", "San Vicente", "Santa Lucia", "Santo Angel", "Santo Niño", "Santol", "Soledad", "Tapucan"],
        "Mulanay": ["Ajos", "Amuguis", "Anonang", "Bagong Silang", "Bagupaye", "Barangay 1", "Barangay 2", "Barangay 3", "Barangay 4", "Bolo", "Buenavista", "Burgos", "Butanyog", "Canuyep", "F. Nanadiego", "Ibabang Cambuga", 
                    "Ibabang Yuni", "Ilayang Cambuga", "Ilayang Yuni", "Latangan", "Magsaysay", "Matataja", "Pakiing", "Patabog", "Sagongon", "San Isidro", "San Pedro", "Santa Rosa"],
        "Padre Burgos": ["Basiao", "Burgos", "Cabuyao Norte", "Cabuyao Sur", "Campo", "Danlagan", "Duhat", "Hinguiwin", "Kinagunan Ibaba", "Kinagunan Ilaya", "Lipata", "Marao", "Marquez", "Punta", "Rizal", "San Isidro", 
                        "San Vicente", "Sipa", "Tulay Buhangin", "Villapaz", "Walay", "Yawe"],
        "Pagbilao": ["Alupaye", "Añato", "Antipolo", "Bantigue", "Barangay 1 Castillo", "Barangay 2 Daungan", "Barangay 3 del Carmen", "Barangay 4 Parang", "Barangay 5 Santa Catalina", "Barangay 6 Tambak", "Bigo", "Binahaan", "Bukal",
                    "Ibabang Bagumbungan", "Ibabang Palsabangon", "Ibabang Polo", "Ikirin", "Ilayang Bagumbungan", "Ilayang Palsabangon", "Ilayang Polo", "Kanluran Malicboy", "Mapagong", "Mayhay", "Pinagbayanan", "Silangan Malicboy", 
                    "Talipan", "Tukalan"],
        "Panukulan": ["Balungay", "Bato", "Bonbon", "Calasumanga", "Kinalagti", "Libo", "Lipata", "Matangkap", "Milawid", "Pagitan", "Pandan", "Rizal", "San Juan"],
        "Patnanungan": ["Amaga", "Busdak", "Kilogan", "Luod", "Patnanungan Norte", "Patnanungan Sur"],
        "Perez": ["Bagong Pag-asa Poblacion", "Bagong Silang Poblacion", "Maabot", "Mainit Norte", "Mainit Sur", "Mapagmahal Poblacion", "Pagkakaisa Poblacion", "Pambuhan", "Pinagtubigan Este", "Pinagtubigan Weste", "Rizal", "Sangirin",
                    "Villamanzano Norte", "Villamanzano Sur"],
        "Pitogo": ["Amontay", "Biga", "Bilucao", "Cabulihan", "Castillo", "Cawayanin", "Cometa", "Dalampasigan", "Dulong Bayan", "Gangahin", "Ibabang Burgos", "Ibabang Pacatin", "Ibabang Piña", "Ibabang Soliyao", "Ilayang Burgos", 
                    "Ilayang Pacatin", "Ilayang Piña", "Ilayang Soliyao", "Maaliw", "Manggahan", "Masaya", "Mayubok", "Nag-Cruz", "Osmeña", "Pag-asa", "Pamilihan", "Payte", "Pinagbayanan", "Poctol", "Quezon", "Quinagasan", "Rizalino",
                    "Saguinsinan", "Sampaloc", "San Roque", "Sisirin", "Sumag Este", "Sumag Norte", "Sumag Weste"],
        "Plaridel": ["Central", "Concepcion", "Duhat", "Ilaya", "Ilosong", "M.L. Tumagay Poblacion", "Paang Bundok", "Pampaaralan", "Tanauan"],
        "Polillo": ["Anawan", "Atulayan", "Balesin", "Bañadero", "Binibitinan", "Bislian", "Bucao", "Canicanian", "Kalubakis", "Languyin", "Libjo", "Pamatdan", "Pilion", "Pinaglubayan", "Poblacion", "Sabang", "Salipsip", "Sibulan",
                    "Taluong", "Tamulaya-Anitong"],
        "Quezon": ["Apad", "Argosino", "Barangay I", "Barangay II", "Barangay III", "Barangay IV", "Barangay V", "Barangay VI", "Cagbalogo", "Caridad", "Cometa", "Del Pilar", "Guinhawa", "Gumubat", "Magsino", "Mascariña", "Montaña", "Sabang",
                    "Silangan", "Tagkawa", "Villa Belen", "Villa Francia", "Villa Gomez", "Villa Mercedes"],
        "Real": ["Bagong Silang", "Capalong", "Cawayan", "Kiloloran", "Llavac", "Lubayat", "Malapad", "Maragondon", "Masikap", "Maunlad", "Pandan", "Poblacion 61", "Poblacion I", "Tagumpay", "Tanauan", "Tignoan", "Ungos"],
        "Sampaloc": ["Alupay", "Apasan", "Banot", "Bataan", "Bayongon", "Bilucao", "Caldong", "Ibabang Owain", "Ilayang Owain", "Mamala", "San Bueno", "San Isidro", "San Roque", "Taquico"],
        "San Andres": ["Alibihaban", "Camflora", "Mangero", "Pansoy", "Poblacion", "Tala", "Talisay"],
        "San Antonio": ["Arawan", "Bagong Niing", "Balat Atis", "Briones", "Bulihan", "Buliran", "Callejon", "Corazon", "Loob", "Magsaysay", "Manuel del Valle , Sr.", "Matipunso", "Niing", "Poblacion", "Pulo", "Pury", "Sampaga", "Sampaguita",
                        "San Jose", "Sinturisan"],
        "San Francisco": ["Butanguiad", "Casay", "Cawayan I", "Cawayan II", "Don Juan Vercelos", "Huyon-Uyon", "Ibabang Tayuman", "Ilayang Tayuman", "Inabuan", "Mabuñga", "Nasalaan", "Pagsangahan", "Poblacion", "Pugon", "Santo Niño", "Silongin"],
        "San Narciso": ["Abuyon", "Andres Bonifacio", "Bani", "Bayanihan", "Binay", "Buenavista", "Busokbusokan", "Calwit", "Guinhalinan", "Lacdayan", "Maguiting", "Maligaya", "Manlampong", "Pagdadamayan", "Pagkakaisa", "Punta",
                        "Rizal", "San Isidro", "San Juan", "San Vicente", "Vigo Central", "Villa Aurin", "Villa Reyes", "White Cliff"],
        "Sariaya": ["Antipolo", "Balubal", "Barangay 1", "Barangay 2", "Barangay 3", "Barangay 4", "Barangay 5", "Barangay 6", "Bignay 1", "Bignay 2", "Bucal", "Canda", "Castañas", "Concepcion Banahaw", "Concepcion No. 1",
                    "Concepcion Palasan", "Concepcion Pinagbakuran", "Gibanga", "Guisguis-San Roque", "Guisguis-Talon", "Janagdong 1", "Janagdong 2", "Limbon", "Lutucan 1", "Lutucan Bata", "Lutucan Malabag", "Mamala I", "Mamala II",
                    "Manggalang 1", "Manggalang Tulo-tulo", "Manggalang-Bantilan", "Manggalang-Kiling", "Montecillo", "Morong", "Pili", "Sampaloc 1", "Sampaloc 2", "Sampaloc Bogon", "Sampaloc Santo Cristo", "Talaan Aplaya", "Talaanpantoc", "Tumbaga 1", "Tumbaga 2"],
        "Tagkawayan": ["Aldavoc", "Aliji", "Bagong Silang", "Bamban", "Bosigon", "Bukal", "Cabibihan", "Cabugwang", "Cagascas", "Candalapdap", "Casispalan", "Colong-colong", "Del Rosario", "Katimo", "Kinatakutan", "Landing", "Laurel",
                        "Magsaysay", "Maguibuay", "Mahinta", "Malbog", "Manato Central", "Manato Station", "Mangayao", "Mansilay", "Mapulot", "Munting Parang", "Payapa", "Poblacion", "Rizal", "Sabang", "San Diego", "San Francisco",
                        "San Isidro", "San Roque", "San Vicente", "Santa Cecilia", "Santa Monica", "Santo Niño I", "Santo Niño II", "Santo Tomas", "Seguiwan", "Tabason", "Tunton", "Victoria"],
        "Tayabas City": ["Alitao", "Alsam Ibaba", "Alsam Ilaya", "Alupay", "Angeles Zone I", "Angeles Zone II", "Angeles Zone III", "Angeles Zone IV", "Angustias Zone I", "Angustias Zone II", "Angustias Zone III", "Angustias Zone IV",
                    "Anos", "Ayaas", "Baguio", "Banilad", "Bukal Ibaba", "Bukal Ilaya", "Calantas", "Calumpang", "Camaysa", "Dapdap", "Domoit Kanluran", "Domoit Silangan", "Gibanga", "Ibas", "Ilasan Ibaba", "Ilasan Ilaya", "Ipilan",
                    "Isabang", "Katigan Kanluran", "Katigan Silangan", "Lakawan", "Lalo", "Lawigue", "Lita", "Malaoa", "Masin", "Mate", "Mateuna", "Mayowe", "Nangka Ibaba", "Nangka Ilaya", "Opias", "Palale Ibaba", "Palale Ilaya", "Palale Kanluran",
                    "Palale Silangan", "Pandakaki", "Pook", "Potol", "San Diego Zone I", "San Diego Zone II", "San Diego Zone III", "San Diego Zone IV", "San Isidro Zone I", "San Isidro Zone II", "San Isidro Zone III", "San Isidro Zone IV",
                    "San Roque Zone I", "San Roque Zone II", "Talolong", "Tamlong", "Tongko", "Valencia", "Wakas"],
        "Tiaong": ["Anastacia", "Aquino", "Ayusan I", "Ayusan II", "Barangay I", "Barangay II", "Barangay III", "Barangay IV", "Behia", "Bukal", "Bula", "Bulakin", "Cabatang", "Cabay", "Del Rosario", "Lagalag", "Lalig", "Lumingon", "Lusacan", "Paiisa",
                    "Palagaran", "Quipot", "San Agustin", "San Francisco", "San Isidro", "San Jose", "San Juan", "San Pedro", "Tagbakin", "Talisay", "Tamisian"],
        "Unisan": ["Almacen", "Balagtas", "Balanacan", "Bonifacio", "Bulo Ibaba", "Bulo Ilaya", "Burgos", "Cabulihan Ibaba", "Cabulihan Ilaya", "Caigdal", "F. de Jesus", "General Luna", "Kalilayan Ibaba", "Kalilayan Ilaya", "Mabini", "Mairok Ibaba",
                    "Mairok Ilaya", "Malvar", "Maputat", "Muliguin", "Pagaguasan", "Panaon Ibaba", "Panaon Ilaya", "Plaridel", "Poctol", "Punta", "R. Lapu-lapu", "R. Magsaysay", "Raja Soliman", "Rizal Ibaba", "Rizal Ilaya", "San Roque", "Socorro", "Tagumpay",
                    "Tubas", "Tubigan"],
    };
    barangaySelect.innerHTML = '<option value="">Select an option</option>';
    if (selectedCity in barangays) {
        for (var i = 0; i < barangays[selectedCity].length; i++) {
            var option = document.createElement('option');
            option.value = barangays[selectedCity][i];
            option.text = barangays[selectedCity][i];
            barangaySelect.appendChild(option);
        }
    }
}
