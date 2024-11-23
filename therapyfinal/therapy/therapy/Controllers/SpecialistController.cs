using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Net.Http.Json;
using System.Threading.Tasks;
using therapy.modelos;

namespace therapy.Controllers
{
    public class SpecialistController
    {
        private readonly HttpClient _httpClient;

        public SpecialistController(HttpClient httpClient)
        {
            _httpClient = httpClient;
        }

        // Obtener todos los especialistas
        public async Task<List<Especialista>> GetSpecialistsAsync()
        {
            return await _httpClient.GetFromJsonAsync<List<Especialista>>("/api/specialists");
        }

        // Obtener un especialista por ID
        public async Task<Especialista> GetSpecialistAsync(int specialistId)
        {
            return await _httpClient.GetFromJsonAsync<Especialista>($"/api/specialist/{specialistId}");
        }

        // Crear un nuevo especialista
        public async Task<bool> CreateSpecialistAsync(Especialista specialist)
        {
            var response = await _httpClient.PostAsJsonAsync("/api/createSpecialist", specialist);
            return response.IsSuccessStatusCode;
        }

        // Actualizar un especialista
        public async Task<bool> UpdateSpecialistAsync(int specialistId, Especialista specialist)
        {
            var response = await _httpClient.PutAsJsonAsync($"/api/updateSpecialist/{specialistId}", specialist);
            return response.IsSuccessStatusCode;
        }

        // Eliminar un especialista
        public async Task<bool> DeleteSpecialistAsync(int specialistId)
        {
            var response = await _httpClient.DeleteAsync($"/api/deleteSpecialist/{specialistId}");
            return response.IsSuccessStatusCode;
        }
    }
}
