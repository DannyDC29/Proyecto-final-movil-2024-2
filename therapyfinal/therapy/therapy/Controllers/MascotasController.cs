using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Net.Http.Json;
using System.Threading.Tasks;
using therapy.modelos;

namespace therapy.Controllers
{
    public class MascotasController
    {
        private readonly HttpClient _httpClient;

        public MascotasController(HttpClient httpClient)
        {
            _httpClient = httpClient;
        }

        // Obtener todas las mascotas
        public async Task<List<Mascota>> GetAnimalsAsync()
        {
            return await _httpClient.GetFromJsonAsync<List<Mascota>>("/api/animals");
        }

        // Obtener una mascota por ID
        public async Task<Mascota> GetAnimalAsync(int animalId)
        {
            return await _httpClient.GetFromJsonAsync<Mascota>($"/api/animal/{animalId}");
        }

        // Crear una nueva mascota
        public async Task<bool> CreateAnimalAsync(Mascota animal)
        {
            var response = await _httpClient.PostAsJsonAsync("/api/createAnimal", animal);
            return response.IsSuccessStatusCode;
        }

        // Actualizar los datos de una mascota
        public async Task<bool> UpdateAnimalAsync(int animalId, Mascota animal)
        {
            var response = await _httpClient.PutAsJsonAsync($"/api/updateAnimal/{animalId}", animal);
            return response.IsSuccessStatusCode;
        }

        // Eliminar una mascota
        public async Task<bool> DeleteAnimalAsync(int animalId)
        {
            var response = await _httpClient.DeleteAsync($"/api/deleteAnimal/{animalId}");
            return response.IsSuccessStatusCode;
        }
    }
}
