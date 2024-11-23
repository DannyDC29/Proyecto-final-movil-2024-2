using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;
using System.Text.Json;
using System.Threading.Tasks;
using therapy.modelos;

namespace therapy.Controllers
{
    public class UserController
    {
        private readonly HttpClient _httpClient;

        public UserController(HttpClient httpClient)
        {
            _httpClient = httpClient;
        }

        // Obtener todos los usuarios
        public async Task<List<User>> GetUsersAsync()
        {
            var response = await _httpClient.GetAsync("/api/users");

            if (response.IsSuccessStatusCode)
            {
                var json = await response.Content.ReadAsStringAsync();
                return JsonSerializer.Deserialize<List<User>>(json);
            }

            throw new Exception("Error al obtener los usuarios");
        }

        // Obtener un usuario por ID
        public async Task<User> GetUserAsync(int usuarioId)
        {
            var response = await _httpClient.GetAsync($"/api/user/{usuarioId}");

            if (response.IsSuccessStatusCode)
            {
                var json = await response.Content.ReadAsStringAsync();
                return JsonSerializer.Deserialize<User>(json);
            }

            throw new Exception("Error al obtener el usuario");
        }

        // Crear un nuevo usuario
        public async Task<bool> CreateUserAsync(User user)
        {
            var json = JsonSerializer.Serialize(user);
            var content = new StringContent(json, Encoding.UTF8, "application/json");

            var response = await _httpClient.PostAsync("/api/createUser", content);

            return response.IsSuccessStatusCode;
        }

        // Actualizar un usuario existente
        public async Task<bool> UpdateUserAsync(int usuarioId, User user)
        {
            var json = JsonSerializer.Serialize(user);
            var content = new StringContent(json, Encoding.UTF8, "application/json");

            var response = await _httpClient.PutAsync($"/api/updateUser/{usuarioId}", content);

            return response.IsSuccessStatusCode;
        }

        // Eliminar un usuario
        public async Task<bool> DeleteUserAsync(int usuarioId)
        {
            var response = await _httpClient.DeleteAsync($"/api/deleteUser/{usuarioId}");

            return response.IsSuccessStatusCode;
        }
    }
}
