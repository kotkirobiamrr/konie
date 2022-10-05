var data = '';

const reservationsHeader = document.getElementById('reservations-header');
const fDate = document.getElementById ('f_date');

function showUpcomingReservation ()
{
	var el = document.getElementById('upcoming-reservation');
	var time = new Date(el.getAttribute('data-timestamp'));
	if (isNaN(time.getTime()))
	{
		el.innerHTML = 'Nie masz przyszłych rezerwacji.'
	}
	else
	{
		el.innerHTML =
			`Twoja najbliższa rezerwacja: <strong>${time.toLocaleString('pl-PL', {
				weekday: 'long',
				day:'numeric',
				month:'long',
				hour:'numeric',
				minute :'2-digit'
			})}</strong>.`;
	}
}
function showSchedule()
{

	const openingHour = data.working_hours.opening_hour;
	const closingHour = data.working_hours.closing_hour;
	const el = document.getElementById('schedule');
	const table = document.getElementById('sched-table');

	var date = new Date(data.date);
	var i,j,k;
	//ustaw nagłówek
	reservationsHeader.innerHTML = `${date.toLocaleString('pl-PL', {
			weekday: 'long',
			day:'numeric',
			month:'long',
		})}`;

	//ustaw wybierak daty
	 document.getElementById('select-date').value =  date.toLocaleDateString('fr-CA');


	//generujemy pustą tabelę
	var tbl = '';
	var tableCols = '';
	var tbl =
		'<thead class="table-dark sticky-top" >'+
			'<tr>'+
				'<th scope="col"></th>';

	for (i = 0; i < data.schedule.length; i++)
	{
		//generujemy nagłówek tabeli
		tbl +=
				`<th scope="col">
					${data.schedule[i].name}
					<div>
						(${data.schedule[i].horse_limit})
					</div>
				</th>`;

		//dodatkowo generujemy tyle komórek dla wiersza, ile mamy placów
		tableCols += `<td data-reservations="[]">0</td>`;

	}
		tbl +=
			'</tr>'+
		'</thead>'+
		'<tbody>';

	//czas do sprawdzania, czy ta pora już minęła
	const now = Date.now() - 1800000;
	const future = Date.now();

	for (date.setHours(openingHour); date.getHours() < closingHour; date.setMinutes(date.getMinutes() + 30))
	{
		//przyciemniamy przeszłe godziny
		if (date.getTime() <  now)
		{
			tbl +=
			'<tr class="past" data-bs-toggle="modal" data-bs-target="#details-modal" onclick="showDetails(this)">';
		}
		else if (date.getTime() < future)	//ale to jeszcze nie jest przyszłość, więc teraźniejszość
		{
			tbl +=
			'<tr class="now" data-bs-toggle="modal" data-bs-target="#details-modal" onclick="showDetails(this)">';
		}
		else
		{
			tbl +=
			'<tr data-bs-toggle="modal" data-bs-target="#details-modal" onclick="showDetails(this)">';
		}

		tbl +=
			`	<th scope="row">${date.toLocaleString('pl-PL', {
					hour:'numeric',
					minute :'2-digit'
			})}</th>` + tableCols +
			"</tr>";
	}
		tbl +=
		'</tbody>';

	table.innerHTML = tbl;



	//wypełniamy tabelę rezerwacjami w danym czasie
	const tableRows = table.tBodies[0].childNodes;
	date = new Date(data.date).setHours(openingHour);
	var reservations;
	var rStartRow;
	var rEndRow;
	var reservationsAttribute;
	for (i = 0; i < data.schedule.length; i++)	//numer placu/kolumny
	{
		reservations = data.schedule[i].reservations;
		for (j = 0; j < reservations.length; j++)
		{
			//liczymy wiersze, w których zaczyna i kończy się rezerwacja
			//1000*60*30 = 1800000
			rStartRow = Math.floor(((new Date(reservations[j].start).getTime()) - date)/1800000);
			rEndRow = Math.ceil(((new Date(reservations[j].end).getTime()) - date)/1800000);

			for (;rStartRow < rEndRow; rStartRow++)
			{
				//aktualizujemy liczbę rezerwacji
				tableRows[rStartRow].children[i+1].innerHTML =
					parseInt (tableRows[rStartRow].children[i+1].innerHTML) +
					reservations[j].horses_count;

				//dodajemy id rezerwacji do atrybutu, by móc później z tego skorzystać
				reservationsAttribute = JSON.parse(tableRows[rStartRow].children[i+1].getAttribute('data-reservations'));
				reservationsAttribute.push (j);
				tableRows[rStartRow].children[i+1].setAttribute('data-reservations', JSON.stringify(reservationsAttribute));

				//informacja, że w tym czasie jest nasza rezerwacja
				if (reservations[j].user.name == data.user.name)
				{
					tableRows[rStartRow].children[0].classList.add ('my');
				}
				//informacja, że to rezerwacja na wyłączność
				if (reservations[j].exclusive == 1)
				{
					tableRows[rStartRow].children[i+1].classList.add ('exclusive');
				}
			}
		}
	}



	//kolorujemy tabelę
	var percent;

	for (i = 0; i < data.schedule.length; i++)
	{
		for (j = 0; j < tableRows.length; j++)
		{

			percent = parseInt(tableRows[j].childNodes[i+2].innerHTML) / parseInt(data.schedule[i].horse_limit);

			if (percent > 0.9 || tableRows[j].childNodes[i+2].classList.contains('exclusive'))
			{
				tableRows[j].childNodes[i+2].classList.add('table-danger');
			}
			else if (percent > 0)
			{
				tableRows[j].childNodes[i+2].classList.add('table-warning');
			}
			/*else if (percent == 0)
			{
				tableRows[j].childNodes[i+2].classList.add(tableReservationColourClass[2]);
			}*/
		}
	}
}

//////////////////////////////////////////////////////////////////
//
//	wypełnij okienko ze szczegółami danego odcinka czasu
//
function showDetails (el)
{
	var mb = '';
	// wypełnij nagłówek godzinami
	var myHorsesCount = 0;
	var horsesCount;
	var hasExclusive;

	var date =  (new Date());
	var z = el.getElementsByTagName('th')[0].innerHTML.split(':');
	date.setHours(z[0]);
	date.setMinutes(z[1]);

	// od
	document.getElementById('details-modal-title').innerHTML =
		`${date.toLocaleString('pl-PL', {
			hour:'numeric',
			minute :'2-digit'
		})} - `;

	date.setMinutes (date.getMinutes()+30);

	// do
	document.getElementById('details-modal-title').innerHTML +=
		`${date.toLocaleString('pl-PL', {
			hour:'numeric',
			minute :'2-digit'
		})} - lista rezerwacji`;


	// pętla po placach
	for (let x = 0; x < data.schedule.length; x++)
	{
		horsesCount = 0;
		hasExclusive = false;

		// odczytaj indeksy rezerwacji pozostawione przez funkcję wypełniającą tabelę
		z = JSON.parse (el.children[x+1].getAttribute('data-reservations'));

			// nagłówek
			mb += 	`<tr>
						<th colspan="6">
							${data.schedule[x].name} (${data.schedule[x].horse_limit})
						</th>
					</tr>`;

		// informacja, jeśli nie ma tu rezerwacji
		if (z.length == 0)
		{
			// mb += 	`<tr>
			// 			<td colspan="6">(brak rezerwacji w tym przedziale czasowym)</td>
			// 		</tr>`;
		}
		else
		{

			// przejdź po rezerwacjach
			for (let y in z)
			{
				let reservation = data.schedule[x].reservations[z[y]];

				mb += '<tr>';

				// nazwa użytkownika i ewentualna informacja, że to nasza rezerwacja
				if (reservation.user.name == data.user.name)
				{
					mb += `	<td class="my" style="color:${reservation.user.role.color}">${reservation.user.name}</td>`;
					myHorsesCount += reservation.horses_count;
				}
				else
				{
					mb += `	<td style="color:${reservation.user.role.color}">${reservation.user.name}</td>`;
				}

				// liczba koni i ewentualna informacja, że to rezerwacja na wyłączność
				horsesCount += reservation.horses_count;
				if (reservation.exclusive == 1)
				{
					hasExclusive = true;
					mb += `	<td class="exclusive">${reservation.horses_count}</td>`;
				}
				else
				{
					mb += `	<td>${reservation.horses_count}</td>`;
				}

				// początek rezerwacji
				date = new Date (reservation.start);
				mb += `	<td>${date.toLocaleString('pl-PL', {
								hour:'numeric',
								minute :'2-digit'
							})}
						</td>`;

				// koniec rezerwacji
				date = new Date (reservation.end);
				mb += `	<td>${date.toLocaleString('pl-PL', {
								hour:'numeric',
								minute :'2-digit'
							})}
						</td>`;

				//komentarz
				mb += `	<td>${reservation.comment}</td>`;

				//////////////////////////////
				//
				//	opcje

				// jeśli ma uprawnienia lub to jego rezerwacja - pokaż guzik do usuwania


				if (data.user.permissions[4] == 1 || data.user.name == reservation.user.name)
				{
					//ale tylko dla przyszłych rezerwacji
					date = new Date (reservation.start);
					if (date.getTime() > Date.now())
						mb += `	<td><button type="button" class="btn btn-danger btn-sm" onclick="deleteReservation(${z[y]},${x})">x</button></td>`;
					else
						mb += `	<td></td>`;

				}
				else
				{
					mb += `	<td></td>`;
				}
				mb += `</tr>`;

			}

		}

		// guzik dodawania rezerwacji
		if (!el.classList.contains ('past') && !el.classList.contains ('now'))	//dodawanie tylko w przyszłości
		{
			if (data.user.permissions[0] == 1)	//czy może dodać rezerwację?
			{
				// jeśli ma uprawnienia do przekraczania limitu lub nie ma rezerwacji na wyłączność i nie przekracza limitu
				// czy przekracza limit swoich koni, sprawdzimy później, ponieważ trzeba przeliczyć wszystkie place

				if (data.user.permissions[1] == 1 ||
					(!hasExclusive
					//&& myHorsesCount < data.user.horses_count
					&& horsesCount < data.schedule[x].horse_limit))
				{
					// dodaj
					mb += `<tr>
							<td colspan="6">
							<button onclick="initializeReservationForm(${data.schedule[x].id}, '${el.getElementsByTagName('th')[0].innerHTML}')" data-bs-dismiss="modal" name="add-reservation-btn" type="button" class="btn btn-success btn-sm">
								Dodaj rezerwację od tej godziny
							</button>
							</td>
						</tr>`;
				}
				else
				{
					// brak miejsc
					mb += `<tr>
							<td colspan="6">
							<button type="button" class="btn btn-secondary btn-sm" disabled>Brak miejsc</button>
							</td>
						</tr>`;
				}
			}
		}
	}
	document.getElementById('details-modal-tbody').innerHTML = mb;

	// jeśli przekroczony jest limit moich koni
	if (data.user.permissions[1] != 1 && myHorsesCount >= data.user.horses_count)
	{

		var btns = document.getElementsByName('add-reservation-btn');

		// wyłączamy wszystkie przyciski
		for (var b = 0; b < btns.length; b++)
		{
			btns[b].disabled = true;
			btns[b].classList.remove ('btn-success');
			btns[b].classList.add ('btn-secondary');
			btns[b].innerHTML = 'Nie możesz dodać więcej rezerwacji w tym czasie'
		}
	}

}

function incrementDate (a)
{
	var date = new Date (data.date);
	date.setDate (date.getDate() + a);
	loadSchedule (date);
}
function setDate (t)
{
	var date = new Date (t.value);
	loadSchedule (date);
}
function loadSchedule (date = new Date())
{

	var headerOld = reservationsHeader.innerHTML;
	reservationsHeader.innerHTML = `<b>Ładowanie:</b> ${date.toLocaleString('pl-PL', {
		weekday: 'long',
		day:'numeric',
		month:'long',
	})}`;

	fetch('/getReservations/'+date.toLocaleDateString('fr-CA'))
		.then(ans => {
			if (ans.ok)
			{
				ans.json().then (json => {
					data = json;
					//ustawiamy datę w formularzu dodawania
					if (fDate != null)
						fDate.value = date.toLocaleDateString('fr-CA');
					showSchedule();
					// jeśli jesteśmy po jakimś błędzie, to pewnie próbowaliśmy dodać rezerwację, odpalamy formularz
					if (typeof(form_errors) != 'undefined')
					{
						showReservationForm();
					}
				});
			}
			else
			{
				alert (`Wystąpił błąd: ${ans.status} ${ans.statusText}\nSpróbuj ponownie później.`);
				reservationsHeader.innerHTML = headerOld;
			}
		})

		.catch(err => {
			alert (`Wystąpił błąd. Sprawdź połączenie z internetem.\n${err}`);
			reservationsHeader.innerHTML = headerOld;
		});
}

function initializeReservationForm (areaId, time)
{
	var time = time.split(':');

	//ustaw godzinę rozpoczęcia
	document.getElementById('f_st_hour').value = parseInt(time[0]);
	document.getElementById('f_st_minute').value = time[1];

	//ustaw godzinę zakończenia 30 minut po rozpoczęciu
	if (time[1] == '00')
	{
		document.getElementById('f_en_hour').value = parseInt(time[0]);
		document.getElementById('f_en_minute').value = '30';
	}
	else
	{
		document.getElementById('f_en_hour').value = parseInt(time[0]) +1 ;
		document.getElementById('f_en_minute').value = '00';
	}

	document.getElementById ('f_area').value = areaId;

	showReservationForm ();
}
function showReservationForm ()
{
	document.getElementById ('reservation-form').classList.remove('d-none');
	checkReservationForm();
}


function checkReservationForm ()
{
	var tbody = document.getElementById('sched-table').tBodies[0];

	var submit = document.getElementById('f-submit');

	var rStHr = document.getElementById('f_st_hour');
	var rStMn = document.getElementById('f_st_minute');

	var rEnHr = document.getElementById('f_en_hour');
	var rEnMn = document.getElementById('f_en_minute');

	var rHorseCnt = document.getElementById('f_horse_count');

	var rArea = document.getElementById('f_area');

	var dataSchedule;
	var col;

	var limits = false;

	var alerts = document.querySelectorAll('#add-reservation-form .alert');
	var my = document.querySelectorAll ('#sched-table .my-add-form');
	var errors = document.querySelectorAll ('#sched-table .error');

	submit.disabled = false;

	// chowamy komunikaty
	for (let a of alerts)
	{
		a.classList.add ('d-none');
		a.classList.add ('alert-danger');
	}

	// usuwamy zaznaczenie godzin
	for (let m of my)
	{
		m.classList.remove ('my-add-form');
	}

	// usuwamy błędy
	for (let e of errors)
	{
		e.classList.remove ('error');
	}


	// zapobiegamy wyjściu poza godzinę zamknięcia
	if (rEnHr.value == data.working_hours.closing_hour)
	{
		rEnMn.value = '00';
	}

	// przeliczamy godziny na wiersze w tabeli
	var rStRow = (parseInt (rStHr.value) - data.working_hours.opening_hour) * 2;
	if (rStMn.value == '30')
		rStRow += 1;

	var rEnRow = (parseInt (rEnHr.value) - data.working_hours.opening_hour) * 2;
	if (rEnMn.value == '30')
		rEnRow += 1;

	// sprawdzamy, czy godzina zakończenia jest poźniejsza niż rozpoczęcia
	if (rStRow >= rEnRow)
	{
		document.getElementById('st-hr-too-late-alert').classList.remove ('d-none');
		submit.disabled = true;
		return;	//nie ma sensu sprawdzać dalej
	}

	if (data.user.permissions[5] != 0 && ((rEnRow - rStRow) > data.user.permissions[5]/30))
	{
		document.getElementById('res-too-long').classList.remove ('d-none');
		submit.disabled = true;
	}

	// szukamy, w której jesteśmy kolumnie
	for (let s in data.schedule)
	{
		if (data.schedule[s].id == rArea.value)
		{
			col = parseInt(s) + 1;
			dataSchedule = data.schedule[s];
			break;
		}
	}

	// przejdź po odpowiednich godzinach w tabeli, by sprawdzić, czy rezerwacja jest poprawna
	for (let x = rStRow; x < rEnRow; x++)
	{
		let td = tbody.children[x].children[col];
		td.classList.add ('my-add-form');

		// rezerwacja zawiera się w przeszłości
		if (td.parentNode.classList.contains ('past') || td.parentNode.classList.contains ('now'))
		{
			document.getElementById ('st-hr-in-past').classList.remove ('d-none');
			td.classList.add ('error');
			submit.disabled = true;
		}

		// obecna rezerwacja na wyłączność
		if (td.classList.contains ('exclusive'))
		{
			limits = true;
			document.getElementById ('excl-res-viol-alert').classList.remove ('d-none');
			td.classList.add ('error');
		}

		// za dużo koni na placu
		if (parseInt(rHorseCnt.value) + parseInt (td.innerHTML) > dataSchedule.horse_limit)
		{
			limits = true;
			document.getElementById ('excd-horse-limit-alert').classList.remove ('d-none');
			td.classList.add ('error');
		}

		// za dużo koni użytkownika w jednym czasie
		var myHorsesCount = parseInt(rHorseCnt.value);	// liczba początkowa to liczba ustawiona w form.
		if (tbody.children[x].children[0].classList.contains('my'))	// jeśli w tym czasie są moje rezerwacje
		{
			for (let y = 1; y <= data.schedule.length; y++)	//przejdź po wszystkich wierszach
			{
				//pobierz tablicę z rezerwacjami w danym odcinku czasu
				let arr = JSON.parse (tbody.children[x].children[y].getAttribute('data-reservations'));
				for (let a in arr)
				{
					// dodaj, jeśli nasze
					if (data.schedule[y-1].reservations[arr[a]].user.name == data.user.name)
					{
						myHorsesCount += parseInt(data.schedule[y-1].reservations[arr[a]].horses_count);
					}
				}
			}
		}
		if (myHorsesCount > data.user.horses_count)
		{
			limits = true;
			document.getElementById ('excd-my-horse-limit-alert').classList.remove ('d-none');
			td.classList.add ('error');
		}

	}

	// jeśli zostały naruszone limity
	if (limits)
	{

		// jeśli ma uprawnienia i zaznaczył checkboxa
		if (data.user.permissions[1] == 1 && document.getElementById ('f_force_limits').checked)
		{
			let alertLimits = document.getElementsByName ('limits')
			for (al of alertLimits)
			{
				al.classList.remove ('alert-danger');
				al.classList.add ('alert-warning');
			}
		}
		else
		{
			submit.disabled = true;
		}
	}
}

function deleteReservation (resId, area)
{
	var r = data.schedule[area].reservations[resId];
	var tStart = new Date(r.start).toLocaleString('pl-PL', {
		hour:'numeric',
		minute :'2-digit'
		});
	var tEnd = new Date(r.end).toLocaleString('pl-PL', {
		hour:'numeric',
		minute :'2-digit'
		});

	var day = new Date(r.start).toLocaleString('pl-PL', {
		weekday: 'long',
		day:'numeric',
		month:'long',
		year:'numeric',
		});

	if (r.exclusive == 1)
	{
		var horses = r.horses_count+'w';
	}
	else
	{
		var horses = r.horses_count;
	}
	var user = '';
	if (data.user.permissions[3] == 1)
	{
		user = 'Użytkownik: '+r.user.name+'\n';
	}

	if (confirm (`Czy na pewno usunąć rezerwację?\n\nMiejsce: ${data.schedule[area].name}\n${user}Dzień: ${day}\nGodziny: ${tStart} - ${tEnd}\nLiczba koni: ${horses}\n${r.comment}`))
	{
		location.href = 'deleteReservation/'+r.id;
	}
}
if (fDate != null && fDate.value != "")// && typeof(form_errors) != 'undefined')
	loadSchedule(new Date(fDate.value));
else
	loadSchedule();

showUpcomingReservation();


