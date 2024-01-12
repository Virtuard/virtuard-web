@php($location_search_style = setting_item('hotel_location_search_style'))

<div class="form-group">
    <div class="form-content">
        <label> {{ $field['title'] }} </label>
        <div class="smart-search d-flex justify-content-between align-items-center">
            <input type="text" aria-label='location_id' class='form-control autocomplete location-business' id='location_id' name='location_id' placeholder="Location" style="border-top: none;border-left:none;border-right:none;">
            <button class="btn btn-sm" id="get-location" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.5397 22.351C11.57 22.3685 11.5937 22.3821 11.6105 22.3915L11.6384 22.4071C11.8613 22.5294 12.1378 22.5285 12.3608 22.4075L12.3895 22.3915C12.4063 22.3821 12.43 22.3685 12.4603 22.351C12.5207 22.316 12.607 22.265 12.7155 22.1982C12.9325 22.0646 13.2388 21.8676 13.6046 21.6091C14.3351 21.0931 15.3097 20.3274 16.2865 19.3273C18.2307 17.3368 20.25 14.3462 20.25 10.5C20.25 5.94365 16.5563 2.25 12 2.25C7.44365 2.25 3.75 5.94365 3.75 10.5C3.75 14.3462 5.76932 17.3368 7.71346 19.3273C8.69025 20.3274 9.66491 21.0931 10.3954 21.6091C10.7612 21.8676 11.0675 22.0646 11.2845 22.1982C11.393 22.265 11.4793 22.316 11.5397 22.351ZM12 13.5C13.6569 13.5 15 12.1569 15 10.5C15 8.84315 13.6569 7.5 12 7.5C10.3431 7.5 9 8.84315 9 10.5C9 12.1569 10.3431 13.5 12 13.5Z" fill="#0F172A" />
                </svg>
            </button>
        </div>
    </div>
</div>

@push('css')
    <style>
        .autocomplete-items {
            position: absolute;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
        }

        .form-control:focus {
            outline: none;
            box-shadow: none;
        }

        .bravo_wrap .bravo_form .smart-search:after {
            display: none;
        }
    </style>
@endpush

@push('js')
    <script>
        let autocomplete = (inp, arr) => {
            let currentFocus;
            inp.addEventListener("input", function(e) {
                let a,
                    b,
                    i,
                    val = this.value;

                closeAllLists();

                if (!val) {
                    return false;
                }

                currentFocus = -1;

                a = document.createElement("DIV");

                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items list-group text-left");

                this.parentNode.appendChild(a);

                for (i = 0; i < arr.length; i++) {
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                        b = document.createElement("DIV");
                        b.setAttribute("class", "list-group-item list-group-item-action");
                        b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                        b.innerHTML += arr[i].substr(val.length);
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                        b.addEventListener("click", function(e) {
                            inp.value = this.getElementsByTagName("input")[0].value;
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                }
            });

            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    currentFocus++;
                    addActive(x);
                } else if (e.keyCode == 38) {
                    currentFocus--;
                    addActive(x);
                } else if (e.keyCode == 13) {
                    e.preventDefault();
                    if (currentFocus > -1) {
                        if (x) x[currentFocus].click();
                    }
                }
            });

            let addActive = (x) => {
                if (!x) return false;
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = x.length - 1;
                x[currentFocus].classList.add("active");
            };

            let removeActive = (x) => {
                for (let i = 0; i < x.length; i++) {
                    x[i].classList.remove("active");
                }
            };

            let closeAllLists = (elmnt) => {
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            };

            document.addEventListener("click", function(e) {
                closeAllLists(e.target);
            });
        };

        let data = [
            "Afghanistan",
            "Albania",
            "Algeria",
            "Andorra",
            "Angola",
            "Anguilla",
            "Antigua & Barbuda",
            "Argentina",
            "Armenia",
            "Aruba",
            "Australia",
            "Austria",
            "Azerbaijan",
            "Bahamas",
            "Bahrain",
            "Bangladesh",
            "Barbados",
            "Belarus",
            "Belgium",
            "Belize",
            "Benin",
            "Bermuda",
            "Bhutan",
        ];

        autocomplete(document.querySelector(".location-business"), data);
        autocomplete(document.querySelector(".location-property"), data);
        autocomplete(document.querySelector(".location-natural"), data);
        autocomplete(document.querySelector(".location-accomodation"), data);
        autocomplete(document.querySelector(".location-cultural"), data);
        autocomplete(document.querySelector(".location-rendering"), data);
        autocomplete(document.querySelector(".location-vehicles"), data);
    </script>
    <script>
        document.getElementById('get-location').addEventListener('click', function() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    alert("Lokasi berhasil diizinkan. Latitude: " + position.coords.latitude + ", Longitude: " + position.coords.longitude);
                });
            } else {
                alert("Geolokasi tidak didukung di peramban Anda.");
            }
        });
    </script>
@endpush
