using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Net.Http.Json;
using System.Threading.Tasks;
using therapy.modelos;

namespace therapy.Controllers
{
    public class LogController
    {
        private readonly HttpClient _httpClient;

        public LogController(HttpClient httpClient)
        {
            _httpClient = httpClient;
        }

        // Obtener todos los registros de la bitácora
        public async Task<List<Bitacora>> GetLogsAsync()
        {
            return await _httpClient.GetFromJsonAsync<List<Bitacora>>("/api/bitacora");
        }

        // Crear un nuevo registro de bitácora
        public async Task<bool> CreateLogAsync(Bitacora log)
        {
            var response = await _httpClient.PostAsJsonAsync("/api/createLog", log);
            return response.IsSuccessStatusCode;
        }
    }
}
