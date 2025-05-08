document.addEventListener('DOMContentLoaded', () => {
  const rutInput = document.getElementById('rutInput');
  const keyboardKeys = document.querySelectorAll('.keyboard-key[data-value]');
  const deleteBtn = document.getElementById('deleteBtn');

  
  keyboardKeys.forEach(btn => {
      btn.addEventListener('click', () => {
          addToRut(btn.dataset.value);
      });
  });

  deleteBtn.addEventListener('click', () => {
      deleteLastChar();
  });

  function addToRut(char) {
      let current = rutInput.value.toUpperCase();

      
      const validChars = ['0','1','2','3','4','5','6','7','8','9','K','-'];
      if (!validChars.includes(char)) return;

      
      if (char === '-' && current.includes('-')) return;

      
      if (current.length >= 12) return;

      current += char;
      rutInput.value = formatRut(current);

      validateRutInput(); 
  }

  function deleteLastChar() {
      let current = rutInput.value;
      if (current.length > 0) {
          rutInput.value = current.slice(0, -1);
          validateRutInput(); 
      }
  }

  function formatRut(rut) {
      
      rut = rut.replace(/[^0-9kK]/g, '').toUpperCase();

      
      let cuerpo = rut.slice(0, -1);
      let dv = rut.slice(-1);

      if (cuerpo.length === 0) return dv;
      cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

      return `${cuerpo}-${dv}`;
  }

  function validateRutInput() {
      const rut = rutInput.value;
      rutInput.classList.remove('is-valid', 'is-invalid');

      if (validateRut(rut)) {
          rutInput.classList.add('is-valid');
      } else {
          rutInput.classList.add('is-invalid');
      }
  }

  function validateRut(rut) {
      rut = rut.replace(/\./g, '').replace('-', '').toUpperCase();

      if (!/^[0-9]+[0-9K]$/.test(rut)) return false;

      let cuerpo = rut.slice(0, -1);
      let dv = rut.slice(-1);

      let suma = 0;
      let multiplo = 2;

      for (let i = cuerpo.length - 1; i >= 0; i--) {
          suma += parseInt(cuerpo.charAt(i)) * multiplo;
          multiplo = multiplo < 7 ? multiplo + 1 : 2;
      }

      let dvEsperado = 11 - (suma % 11);
      let dvFinal = (dvEsperado === 11) ? '0' :
                    (dvEsperado === 10) ? 'K' : String(dvEsperado);

      return dv === dvFinal;
  }
});
