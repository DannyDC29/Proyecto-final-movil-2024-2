using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Net.Http.Json;
using System.Threading.Tasks;
using therapy.modelos;

namespace therapy.Controllers
{
    public class ClientController
    {
        private readonly HttpClient _httpClient;

        public ClientController(HttpClient httpClient)
        {
            _httpClient = httpClient;
        }

        // Obtener todos los clientes
        public async Task<List<Cliente>> GetClientsAsync()
        {
            return await _httpClient.GetFromJsonAsync<List<Cliente>>("/api/clients");
        }

        // Obtener un cliente por ID
        public async Task<Cliente> GetClientAsync(int clientId)
        {
            return await _httpClient.GetFromJsonAsync<Cliente>($"/api/client/{clientId}");
        }

        // Crear un nuevo cliente
        public async Task<bool> CreateClientAsync(Cliente client)
        {
            var response = await _httpClient.PostAsJsonAsync("/api/createClient", client);
            return response.IsSuccessStatusCode;
        }

        // Actualizar los datos de un cliente
        public async Task<bool> UpdateClientAsync(int clientId, Cliente client)
        {
            var response = await _httpClient.PutAsJsonAsync($"/api/updateClient/{clientId}", client);
            return response.IsSuccessStatusCode;
        }

        // Eliminar un cliente
        public async Task<bool> DeleteClientAsync(int clientId)
        {
            var response = await _httpClient.DeleteAsync($"/api/deleteClient/{clientId}");
            return response.IsSuccessStatusCode;
        }

        // Nueva funcionalidad: Verificar si el cliente tiene un animal asignado a su terapia
        public async Task<bool> HasAssignedAnimalAsync(int clientId)
        {
            var response = await _httpClient.GetAsync($"/api/Cliente/HasAssignedAnimal/{clientId}");

            if (response.IsSuccessStatusCode)
            {
                var result = await response.Content.ReadFromJsonAsync<bool>();
                return result;
            }

            return false;
        }
    }
}
