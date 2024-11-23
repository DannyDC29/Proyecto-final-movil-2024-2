using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Net.Http.Json;
using System.Threading.Tasks;
using therapy.modelos;

namespace therapy.Controllers
{
    public class AdminController
    {
        private readonly HttpClient _httpClient;

        public AdminController(HttpClient httpClient)
        {
            _httpClient = httpClient;
        }

        // Obtener todos los administradores
        public async Task<List<Admin>> GetAdminsAsync()
        {
            return await _httpClient.GetFromJsonAsync<List<Admin>>("/api/admins");
        }

        // Obtener un administrador por ID
        public async Task<Admin> GetAdminAsync(int adminId)
        {
            return await _httpClient.GetFromJsonAsync<Admin>($"/api/admin/{adminId}");
        }

        // Crear un administrador
        public async Task<bool> CreateAdminAsync(Admin admin)
        {
            var response = await _httpClient.PostAsJsonAsync("/api/createAdmin", admin);
            return response.IsSuccessStatusCode;
        }

        // Actualizar un administrador
        public async Task<bool> UpdateAdminAsync(int adminId, Admin admin)
        {
            var response = await _httpClient.PutAsJsonAsync($"/api/updateAdmin/{adminId}", admin);
            return response.IsSuccessStatusCode;
        }

        // Eliminar un administrador
        public async Task<bool> DeleteAdminAsync(int adminId)
        {
            var response = await _httpClient.DeleteAsync($"/api/deleteAdmin/{adminId}");
            return response.IsSuccessStatusCode;
        }
    }
}
