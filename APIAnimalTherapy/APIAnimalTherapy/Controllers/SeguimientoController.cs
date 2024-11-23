using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using APIAnimalTherapy.Models;
using APIAnimalTherapy.Dtos;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace APIAnimalTherapy.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class SeguimientoController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public SeguimientoController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Seguimiento
        [HttpGet]
        public async Task<ActionResult<IEnumerable<SeguimientoDTO>>> GetSeguimientos()
        {
            try
            {
                var seguimientos = await _context.Seguimientos
                    .Include(s => s.terapia)
                    .Select(s => new SeguimientoDTO
                    {
                        seguimiento_id = s.seguimiento_id,
                        descripcion = s.descripcion ?? string.Empty,
                        fecha = s.fecha,
                        Terapia_idTerapia = s.Terapia_idTerapia,
                        foto_seguimiento = s.foto_seguimiento ?? new byte[0]
                    })
                    .ToListAsync();

                return Ok(seguimientos);
            }
            catch (Exception ex)
            {
                return StatusCode(500, $"Error interno del servidor: {ex.Message}");
            }
        }

        // GET: api/Seguimiento/5
        [HttpGet("{id}")]
        public async Task<ActionResult<SeguimientoDTO>> GetSeguimiento(int id)
        {
            var seguimiento = await _context.Seguimientos
                .Include(s => s.terapia)
                .Select(s => new SeguimientoDTO
                {
                    seguimiento_id = s.seguimiento_id,
                    descripcion = s.descripcion,
                    fecha = s.fecha,
                    Terapia_idTerapia = s.Terapia_idTerapia,
                    foto_seguimiento = s.foto_seguimiento
                })
                .FirstOrDefaultAsync(s => s.seguimiento_id == id);

            if (seguimiento == null)
            {
                return NotFound();
            }

            return Ok(seguimiento);
        }

        // POST: api/Seguimiento/AddSeguimiento
        [HttpPost("AddSeguimiento")]
        public async Task<IActionResult> AddSeguimiento([FromForm] SeguimientoDTO seguimientoDto)
        {
            try
            {
                // Verificar que la terapia exista y esté activa
                var terapia = await _context.Terapias
                    .FirstOrDefaultAsync(t => t.idTerapia == seguimientoDto.Terapia_idTerapia && t.estado == "activo");

                if (terapia == null)
                {
                    return NotFound("No se encontró una terapia activa para este seguimiento.");
                }

                // Crear el seguimiento
                var seguimiento = new Seguimiento
                {
                    descripcion = seguimientoDto.descripcion,
                    fecha = DateTime.Now, // Fecha generada automáticamente
                    Terapia_idTerapia = seguimientoDto.Terapia_idTerapia,
                    foto_seguimiento = seguimientoDto.foto_seguimiento
                };

                // Guardar en la base de datos
                _context.Seguimientos.Add(seguimiento);
                await _context.SaveChangesAsync();

                return Ok(new { Message = "Seguimiento registrado exitosamente." });
            }
            catch (Exception ex)
            {
                return StatusCode(500, $"Error interno: {ex.Message}");
            }
        }

        // PUT: api/Seguimiento/5
        [HttpPut("{id}")]
        public async Task<IActionResult> UpdateSeguimiento(int id, SeguimientoDTO seguimientoDto)
        {
            if (id != seguimientoDto.seguimiento_id)
            {
                return BadRequest(new { Message = "El ID del seguimiento no coincide" });
            }

            var seguimiento = await _context.Seguimientos.FindAsync(id);
            if (seguimiento == null)
            {
                return NotFound(new { Message = "Seguimiento no encontrado" });
            }

            seguimiento.descripcion = seguimientoDto.descripcion;
            seguimiento.foto_seguimiento = seguimientoDto.foto_seguimiento;

            try
            {
                await _context.SaveChangesAsync();
                return NoContent();
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!SeguimientoExists(id))
                {
                    return NotFound(new { Message = "Seguimiento no encontrado" });
                }
                else
                {
                    return StatusCode(500, "Error de concurrencia al actualizar el seguimiento.");
                }
            }
            catch (Exception ex)
            {
                return StatusCode(500, $"Error interno del servidor: {ex.Message}");
            }
        }

        // DELETE: api/Seguimiento/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteSeguimiento(int id)
        {
            try
            {
                var seguimiento = await _context.Seguimientos.FindAsync(id);
                if (seguimiento == null)
                {
                    return NotFound(new { Message = "Seguimiento no encontrado" });
                }

                _context.Seguimientos.Remove(seguimiento);
                await _context.SaveChangesAsync();

                return NoContent();
            }
            catch (Exception ex)
            {
                return StatusCode(500, $"Error interno del servidor: {ex.Message}");
            }
        }

        private bool SeguimientoExists(int id)
        {
            return _context.Seguimientos.Any(e => e.seguimiento_id == id);
        }
    }
}
