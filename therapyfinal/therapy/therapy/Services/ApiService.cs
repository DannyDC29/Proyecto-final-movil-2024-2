using System;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;
using Newtonsoft.Json;
using therapy.Dtos;
using System.Collections.Generic;
using APIAnimalTherapy.Dtos;
using therapy.modelos;
using therapy.pages;


namespace therapy.Services
{
    public class ApiService
    {
        private readonly HttpClient _httpClient;

        public ApiService()
        {
            _httpClient = new HttpClient
            {
                BaseAddress = new Uri(EnviromentVariables.apiBaseURL)
            };
        }

        // Método para autenticar al usuario
        public async Task<User> AuthenticateUser(LoginDto loginDto)
        {
            try
            {
                var content = new StringContent(JsonConvert.SerializeObject(loginDto), Encoding.UTF8, "application/json");
                var response = await _httpClient.PostAsync("api/User/Authenticate", content);

                if (response.IsSuccessStatusCode)
                {
                    var json = await response.Content.ReadAsStringAsync();
                    return JsonConvert.DeserializeObject<User>(json); // Devuelve un objeto User
                }

                return null; // Devuelve null si la autenticación falla
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error en AuthenticateUser: {ex.Message}");
                return null;
            }
        }



        private void SaveUserData(dynamic user)
        {
            Console.WriteLine($"Datos devueltos: usuario_id = {user.usuario_id}, cliente_id = {user.cliente_id}, correo = {user.correo}");
            Preferences.Set("usuario_id", (int)user.usuario_id);
            Preferences.Set("cliente_id", (int)user.cliente_id); // Guarda cliente_id
            Preferences.Set("correo", (string)user.correo);
        }


        // Método para obtener datos del cliente con usuario relacionado
        public async Task<ClienteWithUserDTO> GetClienteWithUserAsync(int clienteId)
        {
            try
            {
                var response = await _httpClient.GetAsync($"api/Cliente/GetClienteWithUser/{clienteId}");

                if (response.IsSuccessStatusCode)
                {
                    var responseBody = await response.Content.ReadAsStringAsync();
                    return JsonConvert.DeserializeObject<ClienteWithUserDTO>(responseBody);
                }

                Console.WriteLine($"Error: {response.StatusCode}, {await response.Content.ReadAsStringAsync()}");
                return null;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al obtener datos del cliente: {ex.Message}");
                return null;
            }
        }

        // Método para obtener nombre y foto de las mascotas
     

        // Método para asignar un animal a una terapia
        public async Task<bool> AssignAnimalToTherapyAsync(int clienteId, int animalId)
        {
            try
            {
                var response = await _httpClient.PutAsync(
                    $"api/Terapia/AssignAnimalToTherapy?clienteId={clienteId}&animalId={animalId}",
                    null // PUT no requiere cuerpo en este caso
                );

                return response.IsSuccessStatusCode;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al asignar el animal a la terapia: {ex.Message}");
                return false;
            }
        }

        // Método para agregar un seguimiento
        public async Task<bool> AddSeguimientoAsync(SeguimientoDTO seguimientoDto)
        {
            try
            {
                var content = new MultipartFormDataContent
                {
                    { new StringContent(seguimientoDto.descripcion), "descripcion" },
                    { new ByteArrayContent(seguimientoDto.foto_seguimiento), "foto_seguimiento", "seguimiento.jpg" },
                    { new StringContent(seguimientoDto.Terapia_idTerapia.ToString()), "Terapia_idTerapia" }
                };

                var response = await _httpClient.PostAsync("api/Seguimiento/AddSeguimiento", content);
                return response.IsSuccessStatusCode;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al agregar seguimiento: {ex.Message}");
                return false;
            }
        }

        // Método para obtener seguimientos por cliente
        public async Task<List<SeguimientoDTO>> GetSeguimientosByClientAsync(int clienteId)
        {
            try
            {
                var response = await _httpClient.GetAsync($"api/Seguimiento?clienteId={clienteId}");

                if (response.IsSuccessStatusCode)
                {
                    var responseBody = await response.Content.ReadAsStringAsync();
                    return JsonConvert.DeserializeObject<List<SeguimientoDTO>>(responseBody);
                }

                return new List<SeguimientoDTO>();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al obtener seguimientos: {ex.Message}");
                return new List<SeguimientoDTO>();
            }
        }

        // Método para actualizar la preferencia del animal
        public async Task<bool> UpdatePreferenceAsync(int clienteId, RequestPreference preference)
        {
            try
            {
                var content = new StringContent(
                    JsonConvert.SerializeObject( preference ),
                    Encoding.UTF8,
                    "application/json"
                );

                var response = await _httpClient.PutAsync($"api/Cliente/UpdatePreference?clienteId={clienteId}", content);

                return response.IsSuccessStatusCode;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al actualizar la preferencia: {ex.Message}");
                return false;
            }
        }


        // Método para obtener animales filtrados
        public async Task<List<AnimalDTO>> GetFilteredAnimalsAsync(int clienteId)
        {
            try
            {
                var response = await _httpClient.GetAsync($"api/Animal/FilterAnimals/{clienteId}");

                if (response.IsSuccessStatusCode)
                {
                    var responseBody = await response.Content.ReadAsStringAsync();
                    return JsonConvert.DeserializeObject<List<AnimalDTO>>(responseBody);
                }

                return null;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al obtener animales filtrados: {ex.Message}");
                return null;
            }
        }

        ///
        public async Task<bool> HasAssignedAnimalAsync(int clienteId)
        {
            try
            {
                var response = await _httpClient.GetAsync($"api/Cliente/HasAssignedAnimal/{clienteId}");

                if (response.IsSuccessStatusCode)
                {
                    var responseBody = await response.Content.ReadAsStringAsync();
                    var result = JsonConvert.DeserializeObject<ClienteWithAnimalDTO>(responseBody);
                    return result?.TieneAnimalAsignado ?? false;
                }
                return false;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al verificar si el cliente tiene animal asignado: {ex.Message}");
                return false;
            }
        }

        internal async Task<IEnumerable<object>> GetAnimalsNameAndPhotoAsync()
        {
            throw new NotImplementedException();
        }
    }
}
