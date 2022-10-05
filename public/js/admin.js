const form = document.getElementById('edit_form');
const modal_title = document.getElementById('admin-details-modal-title');
const errors = document.getElementById ('af_errors');
const edit_options = document.getElementById('edit_options');
function showUserEditForm (el = null)
{

	var err = document.getElementsByClassName ('is-invalid');
	for (let e of err)
	{
		e.classList.remove ('is-invalid');
	}
	errors.classList.add ('d-none');
	// jeśli edycja - podany parametr
	if (el !== null)
	{
		modal_title.innerHTML = 'Edytuj użytkownika';
		edit_options.classList.remove('d-none');

		form['id'].value = el.getElementsByClassName ('af_id')[0].textContent;
		form['email'].value = el.getElementsByClassName ('af_email')[0].textContent;
		form['name'].value = el.getElementsByClassName ('af_name')[0].textContent;
		form['telephone'].value = el.getElementsByClassName ('af_telephone')[0].textContent;
		form['horses_count'].value = el.getElementsByClassName ('af_horses_count')[0].textContent;

		var role =  el.getElementsByClassName ('af_role')[0].textContent;
		var roleElCh = document.getElementById('role').children;

		for (let r of roleElCh)
		{
			if (r.textContent == role)
			{
				r.selected = true;
				break;
			}
		}
	}
	else
	{
		edit_options.classList.add('d-none');
		form.reset();
		form['id'].value = '';
		form['email'].value = '';
		form['name'].value = '';
		form['telephone'].value = '';
		form['horses_count'].value = 1;

		modal_title.innerHTML = 'Dodaj użytkownika';

	}
}
function deleteUser (route)
{
	if (confirm('Czy na pewno usunąć użytkownika '+form['name'].value+'?\n\nUsunięte zostaną jego wszystkie rezerwacje, ten proces jest nieodwracalny.'))
	{
		location.href = route+'/'+form['id'].value;
	}
}

function showAreaEditForm (el = null)
{

	errors.classList.add ('d-none');
	// jeśli edycja - podany parametr
	if (el !== null)
	{
		modal_title.innerHTML = 'Edytuj miejsce';
		edit_options.classList.remove('d-none');

		form['id'].value = el.getElementsByClassName ('af_id')[0].textContent;
		form['name'].value = el.getElementsByClassName ('af_name')[0].textContent;
		form['horse_limit'].value = el.getElementsByClassName ('af_horse_limit')[0].textContent;
		form['display_order'].value = el.getElementsByClassName ('af_display_order')[0].textContent;

	}
	else
	{
		edit_options.classList.add('d-none');
		form.reset();
		form['id'].value = '';
		form['name'].value = '';
		form['horse_limit'].value = 1;
		form['display_order'].value = 1;
		modal_title.innerHTML = 'Dodaj miejsce';

	}
}
function deleteArea (route)
{
	if (confirm('Czy na pewno usunąć miejsce '+form['name'].value+'?\n\nUsunięte zostaną również wszystkie rezerwacje związane z tym miejscem.'))
	{
		location.href = route+'/'+form['id'].value;
	}
}
