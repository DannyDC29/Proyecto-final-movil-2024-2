using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Net.Http.Json;
using System.Threading.Tasks;
using therapy.modelos;

namespace therapy.Controllers
{
    public class FollowupController
    {
        private readonly HttpClient _httpClient;

        public FollowupController(HttpClient httpClient)
        {
            _httpClient = httpClient;
        }

        // Obtener todos los seguimientos
        public async Task<List<Seguimiento>> GetFollowupsAsync()
        {
            return await _httpClient.GetFromJsonAsync<List<Seguimiento>>("/api/followup");
        }

        // Obtener un seguimiento por ID
        public async Task<Seguimiento> GetFollowupAsync(int followupId)
        {
            return await _httpClient.GetFromJsonAsync<Seguimiento>($"/api/followup/{followupId}");
        }

        // Crear un nuevo seguimiento
        public async Task<bool> CreateFollowupAsync(Seguimiento followup)
        {
            var response = await _httpClient.PostAsJsonAsync("/api/createFollowup", followup);
            return response.IsSuccessStatusCode;
        }

        // Actualizar un seguimiento
        public async Task<bool> UpdateFollowupAsync(int followupId, Seguimiento followup)
        {
            var response = await _httpClient.PutAsJsonAsync($"/api/updateFollowup/{followupId}", followup);
            return response.IsSuccessStatusCode;
        }

        // Eliminar un seguimiento
        public async Task<bool> DeleteFollowupAsync(int followupId)
        {
            var response = await _httpClient.DeleteAsync($"/api/deleteFollowup/{followupId}");
            return response.IsSuccessStatusCode;
        }
    }
}
