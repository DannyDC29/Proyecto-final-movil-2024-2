using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Net.Http.Json;
using System.Threading.Tasks;
using therapy.modelos;

namespace therapy.Controllers
{
    public class TherapyController
    {
        private readonly HttpClient _httpClient;

        public TherapyController(HttpClient httpClient)
        {
            _httpClient = httpClient;
        }

        // Obtener todas las terapias
        public async Task<List<Terapia>> GetTherapiesAsync()
        {
            try
            {
                return await _httpClient.GetFromJsonAsync<List<Terapia>>("/api/therapies");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al obtener terapias: {ex.Message}");
                return null;
            }
        }

        // Obtener una terapia por ID
        public async Task<Terapia> GetTherapyByIdAsync(int idTerapia)
        {
            try
            {
                return await _httpClient.GetFromJsonAsync<Terapia>($"/api/therapy/{idTerapia}");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al obtener la terapia con ID {idTerapia}: {ex.Message}");
                return null;
            }
        }

        // Crear una nueva terapia
        public async Task<bool> CreateTherapyAsync(Terapia newTherapy)
        {
            try
            {
                var response = await _httpClient.PostAsJsonAsync("/api/createTherapy", newTherapy);
                return response.IsSuccessStatusCode;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al crear una nueva terapia: {ex.Message}");
                return false;
            }
        }

        // Actualizar una terapia existente
        public async Task<bool> UpdateTherapyAsync(int idTerapia, Terapia updatedTherapy)
        {
            try
            {
                var response = await _httpClient.PutAsJsonAsync($"/api/updateTherapy/{idTerapia}", updatedTherapy);
                return response.IsSuccessStatusCode;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al actualizar la terapia con ID {idTerapia}: {ex.Message}");
                return false;
            }
        }

        // Eliminar una terapia por ID
        public async Task<bool> DeleteTherapyAsync(int idTerapia)
        {
            try
            {
                var response = await _httpClient.DeleteAsync($"/api/deleteTherapy/{idTerapia}");
                return response.IsSuccessStatusCode;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al eliminar la terapia con ID {idTerapia}: {ex.Message}");
                return false;
            }
        }
    }
}
